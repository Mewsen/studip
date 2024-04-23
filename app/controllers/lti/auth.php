<?php
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

    /**
     * Callback function being called before an action is executed.
     */
    public function before_filter(&$action, &$args)
    {
        $this->allow_nobody = false;
        $this->with_session = true;
        if ($action === 'jwks') {
            $this->allow_nobody = true;
            $this->with_session = false;
        }
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
                WHERE user_id = ? AND seminar_user.status IN ('dozent', 'tutor')
                ORDER BY start_time DESC, Name";
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
    public function oidc_init_action()
    {
        require_once 'lib/elearning/lti1.3a/RegistrationManager.class.php';
        $reg_manager = new \Studip\LTI13a\RegistrationManager();
        $user_authenticator = new \Studip\LTI13a\UserAuthenticator();
        $request = $this->getPsrRequest();

        //DEBUG:
        $logger = new \Monolog\Logger('lti13a', [new \Monolog\Handler\StreamHandler($GLOBALS['TMP_PATH'] . '/lti13a_debug.log', \Monolog\Logger::DEBUG)]);

        $user_authenticator->setLogger($logger);
        $oidc_handler = new \OAT\Library\Lti1p3Core\Security\Oidc\Server\OidcAuthenticationRequestHandler(
            new \OAT\Library\Lti1p3Core\Security\Oidc\OidcAuthenticator(
                $reg_manager,
                $user_authenticator,
                //The following is necessary due to a library bug.
                //See: https://github.com/oat-sa/lib-lti1p3-core/issues/154
                new \OAT\Library\Lti1p3Core\Message\Payload\Builder\MessagePayloadBuilder(
                    new \Studip\LTI13a\NonceGenerator(true)
                )
            ),
            null,
            $logger
        );
        $response = $oidc_handler->handle($request);
        $this->renderPsrResponse($response);
    }

    /**
     * This action handles JSON web key set (JWKS) requests for the platform key.
     *
     * @return void
     */
    public function jwks_action()
    {
        $repo = new \OAT\Library\Lti1p3Core\Security\Key\KeyChainRepository();
        $keyring = Keyring::findOneBySQL("`range_type` = 'global' AND `range_id` = 'lti13a_platform'");
        if ($keyring) {
            $repo->addKeyChain($keyring->toKeyChain());
        }
        $handler = new \OAT\Library\Lti1p3Core\Security\Jwks\Server\JwksRequestHandler(
            new \OAT\Library\Lti1p3Core\Security\Jwks\Exporter\JwksExporter($repo)
        );
        $response = $handler->handle('lti13a_platform');
        $this->renderPsrResponse($response);
    }


    public function validate_tool_launch_action(string $tool_id)
    {
        $tool = LtiTool::find($tool_id);
        if (!$tool) {
            $this->response->set_status(404);
            $this->render_text(_('Das angegebene LTI Tool wurde nicht gefunden.'));
            return;
        }

        //TODO: Create a PSR-7 request object,
        //fill it with data, send it to the library
        //and output its response.
        //See: https://oat-sa.github.io/doc-lti1p3/libraries/lib-lti1p3-core/doc/message/platform-originating-messages/

        $registration = new \OAT\Library\Lti1p3Core\Registration\Registration(
            $tool->id,
            'TODO', //TODO
            \Studip\LTI13a\PlatformManager::getPlatformConfiguration(),
            $tool,
            [] //TODO
        );

        $nonce = null; //TODO

        $validator = new \OAT\Library\Lti1p3Core\Message\Launch\Validator\Tool\ToolLaunchValidator(
            $registration,
            $nonce
        );

        $result = $validator->validatePlatformOriginatingLaunch($this->getPsrRequest());
        if ($result->hasError()) {
            //TODO: Display the error message.
            return;
        }
        //TODO: Output the result.
    }

    public function platform_data_action()
    {
        $this->platform = \Studip\LTI13a\PlatformManager::getPlatformConfiguration();
        $this->render_template('lti/_platform_data');
    }
}
