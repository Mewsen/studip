<?php


class Lti13aController extends StudipController
{
    use Studip\OAuth2\NegotiatesWithPsr7;


    public function before_filter(&$action, &$args)
    {
        $this->allow_nobody = false;
        $this->with_session = true;
        if ($action === 'jwks') {
            $this->allow_nobody = true;
            $this->with_session = false;
        }
        parent::before_filter($action, $args);
    }

    public function oidc_init_action()
    {
        require_once 'lib/elearning/lti1.3a/RegistrationManager.class.php';
        $reg_manager = new \Studip\Lti13a\RegistrationManager();
        $user_authenticator = new \Studip\LTI13a\UserAuthenticator();
        $request = $this->getPsrRequest();
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
            new \Monolog\Logger('lti13a', [new \Monolog\Handler\StreamHandler($GLOBALS['TMP_PATH'] . '/lti13a_debug.log', \Monolog\Logger::DEBUG)])
        );
        $response = $oidc_handler->handle($request);
        $this->renderPsrResponse($response);

    }

    /**
     * Handles JSON web key set (JWKS) requests for the platform key.
     *
     * @return void
     */
    public function jwks_action()
    {
        $handler = new \OAT\Library\Lti1p3Core\Security\Jwks\Server\JwksRequestHandler(
            new \OAT\Library\Lti1p3Core\Security\Jwks\Exporter\JwksExporter(
                new Studip\LTI13a\KeyManager()
            )
        );

        $response = $handler->handle('lti13a_platform');
        $this->renderPsrResponse($response);
    }

    public function oauth2_token_action()
    {
        die('not yet implemented');
    }


    public function deep_linking_action()
    {

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
}
