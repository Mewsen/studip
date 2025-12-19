<?php
require_once __DIR__ . '/AdminBaseController.php';

use LTI\AdminBaseController;
use Lti\Deployment;
use Lti\Registration;
use Lti\ResourceLink;

class Admin_Lti_ResourcesController extends AdminBaseController
{
    public function create_action()
    {
        PageLayout::setTitle(_('LTI-Resource hinzufügen'));

        $this->render_vue_app(
            Studip\VueApp::create('lti/resources/Create')
                ->withProps([
                    'registrations' => $this->getTransformedRegistrations()
                ])
        );
    }

    public function store_action(): void
    {
        CSRFProtection::verifyUnsafeRequest();

        $registration = Registration::find(Request::get('registration_id'));

        $deployment = Deployment::create([
            'name' => Request::get('title', $registration->name),
            'registration_id' => $registration->id,
            'deployment_id' => bin2hex(random_bytes(6)),
            'client_id' => $registration->getDefaultDeployment()->client_id
        ]);

        $resourceLink = ResourceLink::create([
            'deployment_id' => $deployment->id,
            'course_id' => $this->range_id,
            'title' => Request::get('title'),
            'description' => Request::get('description'),
            'custom_parameters' => Request::get('custom_parameters'),
//            'color' => Request::get('color'),
//            'icon' => Request::get('icon')
        ]);

        PageLayout::postSuccess(
            sprintf(
                _('Der LTI-Ressource „%s“ wurde hinzugefügt.'),
                htmlReady($resourceLink->title)
            )
        );

        $this->redirect('course/lti');
    }

    public function edit_action(ResourceLink $resourceLink): void
    {
        PageLayout::setTitle(_('LTI-Ressource bearbeiten'));

        $this->render_vue_app(
            Studip\VueApp::create('lti/resources/Edit')
                ->withProps([
                    'resource' => $resourceLink->transformData(['registration']),
                    'registrations' => $this->getTransformedRegistrations()
                ])
        );
    }

    public function update_action(ResourceLink $resourceLink): void
    {
        CSRFProtection::verifyUnsafeRequest();

        $resourceLink->setData([
            'title' => Request::get('title'),
            'description' => Request::get('description'),
            'custom_parameters' => Request::get('custom_parameters'),
//            'color' => Request::get('color'),
//            'icon' => Request::get('icon')
        ]);

        $resourceLink->store();

        PageLayout::postSuccess(
            sprintf(
                _('Der LTI-Ressource „%s“ wurde gespeichert.'),
                htmlReady($resourceLink->title)
            )
        );

        $this->redirect('course/lti');
    }

    public function delete_action(ResourceLink $resourceLink): void
    {
        CSRFProtection::verifyUnsafeRequest();

        $resourceTitle = $resourceLink->title;
        $resourceLink->delete();

        PageLayout::postSuccess(
            sprintf(
                _('Der LTI-Ressource „%s“ wurde gelöscht.'),
                htmlReady($resourceTitle)
            )
        );

        $this->redirect('course/lti');
    }

    private function getTransformedRegistrations(): array
    {
        $registrations = Registration::findBySQL(
            "`role`= 'tool' AND `state` = 1 AND `range_id` IN (:range_ids) ORDER BY `mkdate`, `name`",
            [
                'range_ids' => [$this->range_id, 'global']
            ]
        );

        return array_map(fn ($r) => $r->transformData(), $registrations);
    }

}
