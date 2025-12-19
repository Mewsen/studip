<?php
use Studip\Cache\Factory;
use Studip\LTI13a\KeyManager;
use Studip\LTI13a\PlatformManager;
use Studip\LTI13a\RegistrationManager;
use Studip\LTI13a\UserAuthenticator;
use Studip\OAuth2\Bridge\ScopeEntity;
use OAT\Library\Lti1p3Core\Security\Jwks\Exporter\JwksExporter;
use OAT\Library\Lti1p3Core\Security\Jwks\Server\JwksRequestHandler;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainRepository;
use OAT\Library\Lti1p3Core\Security\OAuth2\Factory\AuthorizationServerFactory;
use OAT\Library\Lti1p3Core\Security\OAuth2\Generator\AccessTokenResponseGenerator;
use OAT\Library\Lti1p3Core\Security\OAuth2\Repository\AccessTokenRepository;
use OAT\Library\Lti1p3Core\Security\OAuth2\Repository\ClientRepository;
use OAT\Library\Lti1p3Core\Security\OAuth2\Repository\ScopeRepository;
use OAT\Library\Lti1p3Core\Security\Oidc\OidcAuthenticator;
use OAT\Library\Lti1p3Core\Security\Oidc\Server\OidcAuthenticationRequestHandler;
use Studip\OAuth2\NegotiatesWithPsr7;
use Trails\Dispatcher;

class Lti_AuthController extends StudipController
{
    use NegotiatesWithPsr7;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->allow_nobody = false;
        $action = basename(get_route());
        if (in_array($action, ['jwks', 'token'])) {
            $this->allow_nobody = true;
            $this->with_session = $action !== 'jwks';
        }
        parent::__construct($dispatcher);
    }

    /**
     * Callback function being called before an action is executed.
     */
    public function before_filter(&$action, &$args)
    {
        if (in_array($action, ['index', 'content_item', 'link_content_item'])) {
            // enforce LTI SSO login
            Request::set('sso', 'lti');
        }
        parent::before_filter($action, $args);
    }

    /**
     * Redirect to enrolment action for the given course, if needed.
     */
    public function index_action($course_id = null)
    {
        $course_id = Request::option('custom_cid', $course_id);
        $course_id = Request::option('custom_course', $course_id);
        $message_type = Request::option('lti_message_type');

        if ($message_type === 'ContentItemSelectionRequest') {
            $_SESSION['ContentItemSelection'] = [
                'oauth_consumer_key' => Request::get('oauth_consumer_key'),
                'content_item_return_url' => Request::get('content_item_return_url'),
                'document_targets' => Request::get('accept_presentation_document_targets'),
                'data' => Request::get('data')
            ];
            $this->redirect('lti/content_item');
        } else if ($course_id) {
            $this->redirect('course/enrolment/apply/' . $course_id);
        } else {
            $this->redirect('start');
        }
    }

    /**
     * Select course for ContentItemSelectionRequest message.
     */
    public function content_item_action()
    {
        PageLayout::setTitle(_('Veranstaltung verknüpfen'));
        Navigation::activateItem('/browse/my_courses/content_item');

        $this->document_targets = $_SESSION['ContentItemSelection']['document_targets'];
        $this->target_labels = [
            'embed'   => _('in Seite einbetten'),
            'frame'   => _('gleiches Fenster oder Tab'),
            'iframe'  => _('IFrame in der Seite'),
            'window'  => _('neues Fenster oder Tab'),
            'popup'   => _('Popup-Fenster'),
            'overlay' => _('Dialog'),
            'none'    => _('nicht anzeigen')
        ];

        $sql = "JOIN seminar_user USING(Seminar_id)
                LEFT JOIN semester_courses sc ON seminare.seminar_id = sc.course_id
                LEFT JOIN semester_data s USING (semester_id)
                WHERE user_id = ? AND seminar_user.status IN ('dozent', 'tutor')
                ORDER BY s.beginn DESC, Name";
        $this->courses = Course::findBySQL($sql, [$GLOBALS['user']->id]);
    }

    /**
     * Return the selected content item to the LTI consumer.
     */
    public function link_content_item_action()
    {
        CSRFProtection::verifyUnsafeRequest();
        $course_id = Request::option('course_id');
        $target = Request::option('target');
        $course = Course::find($course_id);

        $consumer_key = $_SESSION['ContentItemSelection']['oauth_consumer_key'];
        $return_url = $_SESSION['ContentItemSelection']['content_item_return_url'];
        $data = $_SESSION['ContentItemSelection']['data'];
        unset($_SESSION['ContentItemSelection']);

        $consumer_config = $GLOBALS['STUDIP_AUTH_CONFIG_LTI']['consumer_keys'][$consumer_key];
        $consumer_secret = $consumer_config['consumer_secret'];
        $signature_method = $consumer_config['signature_method'] ?? 'sha1';

        $content_items = [
            '@context' => 'http://purl.imsglobal.org/ctx/lti/v1/ContentItem',
            '@graph' => []
        ];

        if (Request::submitted('link')) {
            $content_items['@graph'][] = [
                '@type' => 'LtiLinkItem',
                'mediaType' => 'application/vnd.ims.lti.v1.ltilink',
                'title' => $course->name,
                'text' => $course->beschreibung,
                'placementAdvice' => ['presentationDocumentTarget' => $target],
                'custom' => ['course' => $course_id]
            ];
        }

        // set up ContentItemSelection
        $lti_link = new LtiLink($return_url, $consumer_key, $consumer_secret, $signature_method);
        $lti_link->addLaunchParameters([
            'lti_message_type' => 'ContentItemSelection',
            'content_items' => json_encode($content_items),
            'data' => $data
        ]);

        $this->launch_url = $lti_link->getLaunchURL();
        $this->launch_data = $lti_link->getBasicLaunchData();
        $this->signature = $lti_link->getLaunchSignature($this->launch_data);
        $this->render_template('course/lti/iframe');
    }

    /**
     * OIDC login
     *
     * @return void
     */
    public function login_action(): void
    {
        $oidcLoginHandler = new OidcAuthenticationRequestHandler(
            new OidcAuthenticator(
                new RegistrationManager(),
                new UserAuthenticator()
            )
        );

        $response = $oidcLoginHandler->handle($this->getPsrRequest());
        $this->renderPsrResponse($response);
    }

    /**
     * This action handles JSON web key set (JWKS) requests for the platform key.
     *
     * @return void
     */
    public function jwks_action(): void
    {
        $keyChainRepo = new KeyChainRepository();
        $platformKeyring = PlatformManager::getKeyring();

        $keyChainRepo->addKeyChain($platformKeyring->toKeyChain());
        $handler = new JwksRequestHandler(new JwksExporter($keyChainRepo));
        $this->renderPsrResponse($handler->handle($platformKeyring->range_id));
    }

    /**
     * Generates OAuth2 tokens for LTI tools.
     */
    public function token_action(): void
    {
        $platformEncryptionKey = PlatformManager::getPrivateKey()->getContent();
        $responseGenerator = new AccessTokenResponseGenerator(
            new KeyManager(),
            new AuthorizationServerFactory(
                new ClientRepository(new RegistrationManager()),
                new AccessTokenRepository(Factory::getCache()),
                new ScopeRepository(
                    [
                        new ScopeEntity('https://purl.imsglobal.org/spec/lti-ags/scope/lineitem'),
                        new ScopeEntity('https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly'),
                        new ScopeEntity('https://purl.imsglobal.org/spec/lti-ags/scope/score')
                    ]
                ),
                $platformEncryptionKey
            )
        );

        $response = $responseGenerator->generate(
            $this->getPsrRequest(),
            $this->getPsrResponse(),
            '1'
        );

        $this->renderPsrResponse($response);
    }
}
