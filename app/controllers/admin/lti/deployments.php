<?php
require_once __DIR__ . '/AdminBaseController.php';

use LTI\AdminBaseController;
use Lti\Deployment;
use Lti\Registration;
use Ramsey\Uuid\Uuid;

class Admin_Lti_DeploymentsController  extends AdminBaseController
{
    public function index_action(): void
    {
        $registration = Registration::find(Request::option('registration_id'));

        if (!$registration) {
            throw new Exception('Missing or invalid registration ID!');
        }

        PageLayout::setTitle(_(sprintf('LTI-Deployments „%s“', htmlReady($registration->name))));

        $this->render_vue_app(
            Studip\VueApp::create('lti/deployments/Index')
                ->withProps([
                    'registration' => $registration->transformData(),
                    'deployments' => $registration->deployments->transformData()
                ])
        );
    }

    public function create_action(): void
    {
        PageLayout::setTitle(_('Neues Deployment anlegen'));
        $this->render_vue_app(
            Studip\VueApp::create('lti/deployments/Create')
                ->withProps([
                    'registration' => Registration::find(Request::option('registration_id'))->transformData()
                ])
        );
    }

    public function store_action(): void
    {
        CSRFProtection::verifyUnsafeRequest();

        $deployment = Deployment::create([
            'name' => Request::get('name'),
            'registration_id' => Request::get('registration_id'),
            'deployment_id' => Request::get('deployment_id'),
            'client_id' => Request::get('client_id', Uuid::uuid4()->toString())
        ]);

        PageLayout::postSuccess(
            sprintf(
                _('Das LTI-Deployment „%s“ wurde gespeichert.'),
                htmlReady($deployment->name)
            )
        );

        $this->redirect('admin/lti/deployments', ['registration_id' => $deployment->registration_id]);
    }

    public function delete_action(Deployment $deployment): void
    {
        CSRFProtection::verifyUnsafeRequest();

        $deploymentName = $deployment->name;
        $registrationId = $deployment->registration_id;
        $deployment->delete();

        PageLayout::postSuccess(
            sprintf(
                _('Die LTI-Registrierung „%s“ wurde gelöscht.'),
                htmlReady($deploymentName)
            )
        );

        $this->redirect('admin/lti/deployments', ['registration_id' => $registrationId]);
    }
}
