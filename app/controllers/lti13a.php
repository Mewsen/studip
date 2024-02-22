<?php


class Lti13aController extends StudipController
{
    use Studip\OAuth2\NegotiatesWithPsr7;


    public function oidc_init_action()
    {
        $request = $this->getPsrRequest();
        $reg_manager = new \Studip\Lti13a\RegistrationManager();
        $nonce_repo = new \OAT\Library\Lti1p3Core\Security\Nonce\NonceRepository(
            StudipCacheFactory::getCache()
        );

        $validator = new \OAT\Library\Lti1p3Core\Message\Launch\Validator\Platform\PlatformLaunchValidator(
            $reg_manager,
            $nonce_repo
        );
        $result = $validator->validateToolOriginatingLaunch($request);

        //Find out where to redirect to:
        $deployment_ids = $result->getRegistration()->getDeploymentIds();
        if (count($deployment_ids) != 1) {
            PageLayout::postError(_('Die LTI Deployment-ID ist ungültig.'));
            return;
        }
        $lti_deployment = LtiData::find($deployment_ids[0]);
        if (!$lti_deployment) {
            PageLayout::postError(_('Die LTI Deployment-ID ist ungültig.'));
            return;
        }
        $redirect_path = $this->url_for('course/lti', ['cid' => $lti_deployment->course_id]);


        if ($result->hasError()) {
            PageLayout::postError(
                _('Ein Fehler trat bei der OIDC-Initialisierung auf:'),
                [$result->getError()]
            );
            $this->redirect($redirect_path);
        }

        if ($result->getVersion() !== '1.3.0') {
            PageLayout::postError(_('Die LTI-Version wird nicht unterstüttz.'));
            $this->redirect($redirect_path);
        }
        PageLayout::postSuccess(
            _('LTI OIDC Initialisierung erfolgreich. Debug-Meldungen:'),
            $result->getSuccesses()
        );
        $this->redirect($redirect_path);
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
