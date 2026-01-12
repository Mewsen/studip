<?php
require_once __DIR__ . '/AdminBaseController.php';

use LTI\AdminBaseController;
use Lti\Deployment;
use Lti\Registration;
use Lti\ResourceLink;
use Studip\Lti\Enum\RegistrationStatus;

class Admin_Lti_ResourcesController extends AdminBaseController
{
    public function create_action()
    {
        PageLayout::setTitle(_('LTI-Resource hinzufügen'));

        $this->render_vue_app(
            Studip\VueApp::create('lti/resources/Create')
                ->withProps([
                    'registrations' => $this->getTransformedRegistrations(),
                    'icons' => $this->getStudipIcons()
                ])
        );
    }

    public function store_action(): void
    {
        CSRFProtection::verifyUnsafeRequest();

        $registration = Registration::find(Request::get('registration_id'));

        if (Request::get('launch_type') === 'deep_linking') {
            $deployment = Deployment::create([
                'name' => Request::get('title', $registration->name),
                'registration_id' => $registration->id,
                'deployment_key' => bin2hex(random_bytes(6)),
                'client_id' => $registration->getDefaultDeployment()->client_id
            ]);
        } else {
            $deployment = $registration->getDefaultDeployment();
        }

        $resourceLink = ResourceLink::create([
            'deployment_id' => $deployment->id,
            'course_id' => $this->range_id,
            'title' => Request::get('title'),
            'description' => Request::get('description'),
            'custom_parameters' => Request::get('custom_parameters'),
            'launch_container' => Request::get('launch_container', 'window'),
            'launch_type' => Request::get('launch_type', 'default'),
            'color' => Request::get('color'),
            'icon' => Request::get('icon')
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
                    'registrations' => $this->getTransformedRegistrations(),
                    'icons' => $this->getStudipIcons()
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
            'launch_container' => Request::get('launch_container', $resourceLink->launch_container),
            'launch_type' => Request::get('launch_type', $resourceLink->launch_type),
            'color' => Request::get('color'),
            'icon' => Request::get('icon')
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

        $deploymentsCount = ResourceLink::countBySql("deployment_id = ?", [$resourceLink->deployment_id]);

        if ($this->launch_type !== 'deep_linking' && $deploymentsCount === 1 && !$resourceLink->deployment->is_default) {
            $resourceLink->deployment->delete();
        }

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
            "`role`= 'tool' AND `status` = :status AND `range_id` IN (:range_ids) ORDER BY `mkdate`, `name`",
            [
                'status' => RegistrationStatus::Active->value,
                'range_ids' => [$this->range_id, 'global']
            ]
        );

        return array_map(fn ($r) => $r->transformData(), $registrations);
    }

    private function getStudipIcons(): array
    {
        $icons = [];

        foreach (scandir($GLOBALS['STUDIP_BASE_PATH'] . '/public/assets/images/icons/blue') as $iconPath) {
            if (!is_dir(
                    $GLOBALS['STUDIP_BASE_PATH'] . '/public/assets/images/icons/blue/'
                ) . $iconPath && $iconPath[0] !== '.') {
                if (stripos($iconPath, '.svg')) {
                    $iconPath = substr($iconPath, 0, stripos($iconPath, '.svg'));
                }
                $icons[] = $iconPath;
            }
        }

        return array_unique($icons);
    }

}
