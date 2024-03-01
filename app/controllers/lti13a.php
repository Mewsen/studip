<?php


class Lti13aController extends AuthenticatedController
{
    use Studip\OAuth2\NegotiatesWithPsr7;


    public function oidc_init_action()
    {
        require_once 'lib/elearning/lti1.3a/RegistrationManager.class.php';
        $reg_manager = new \Studip\Lti13a\RegistrationManager();
        $user_authenticator = new \Studip\LTI13a\UserAuthenticator();
        $request = $this->getPsrRequest();
        $oidc_handler = new \OAT\Library\Lti1p3Core\Security\Oidc\Server\OidcAuthenticationRequestHandler(
            new \OAT\Library\Lti1p3Core\Security\Oidc\OidcAuthenticator(
                $reg_manager,
                $user_authenticator
            )
        );

        $response = $oidc_handler->handle($request);
        $this->renderPsrResponse($response);
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
