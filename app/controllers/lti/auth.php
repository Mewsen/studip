<?php

use OAT\Library\Lti1p3Core\Message\Payload\Builder\MessagePayloadBuilder;
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
use Studip\Cache\Factory;
use Studip\LTI13a\KeyManager;
use Studip\LTI13a\NonceGenerator;
use Studip\LTI13a\PlatformManager;
use Studip\LTI13a\RegistrationManager;
use Studip\LTI13a\UserAuthenticator;
use Studip\OAuth2\Bridge\ScopeEntity;

/**
 * auth.php - LTI authentication controller
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Elmar Ludwig
 * @author      Moritz Strohm
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */

class Lti_AuthController extends StudipController
{
    use Studip\OAuth2\NegotiatesWithPsr7;

    public function __construct(\Trails\Dispatcher $dispatcher)
    {
        $this->allow_nobody = false;
        $action = basename(get_route());
        if (in_array($action, ['jwks', 'oauth2_token'])) {
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
     * This action handles OIDC (OpenID connect) requests.
     *
     * @return void
     */
    public function oidc_init_action(): void
    {
        $reg_manager = new RegistrationManager();
        $user_authenticator = new UserAuthenticator();
        $request = $this->getPsrRequest();

        $oidc_handler = new OidcAuthenticationRequestHandler(
            new OidcAuthenticator(
                $reg_manager,
                $user_authenticator,
                //The following is necessary due to a library bug.
                //See: https://github.com/oat-sa/lib-lti1p3-core/issues/154
                new MessagePayloadBuilder(new NonceGenerator(true))
            )
        );
        $response = $oidc_handler->handle($request);
        $this->renderPsrResponse($response);
    }

    /**
     * This action handles JSON web key set (JWKS) requests for the platform key.
     *
     * @return void
     */
    public function jwks_action(): void
    {
        $repo = new KeyChainRepository();
        $keyring = Keyring::findOneBySQL("`range_type` = 'global' AND `range_id` = 'lti13a_platform'");
        if ($keyring) {
            $repo->addKeyChain($keyring->toKeyChain());
        }
        $handler = new JwksRequestHandler(new JwksExporter($repo));
        $response = $handler->handle('lti13a_platform');
        $this->renderPsrResponse($response);
    }

    /**
     * Generates OAuth2 tokens for LTI tools.
     */
    public function oauth2_token_action(): void
    {
        $keyring = Keyring::findOneByRange_id('lti13a_platform');
        if (!$keyring) {
            throw new \Studip\Exception(
                'Stud.IP LTI 1.3a platform keyring cannot be found!'
            );
        }
        $key_chain = $keyring->toKeyChain();
        $response_generator = new AccessTokenResponseGenerator(
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
                $key_chain->getPrivateKey()->getContent()
            )
        );

        $response = $response_generator->generate(
            $this->getPsrRequest(),
            $this->getPsrResponse(),
            'lti13a_platform'
        );

        $this->renderPsrResponse($response);
    }

    /**
     * Displays LTI platform data of the Stud.IP installation. The data are needed for configuring the
     * platform on the tool side.
     */
    public function platform_data_action()
    {
        $this->platform = PlatformManager::getPlatformConfiguration();
        $this->render_template('lti/_platform_data');
    }
}
