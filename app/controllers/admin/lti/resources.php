<?php
require_once __DIR__ . '/AdminBaseController.php';

use LTI\AdminBaseController;
use Lti\Deployment;
use Lti\Registration;
use Lti\ResourceLink;
use Studip\Lti\Enum\RegistrationStatus;

class Admin_Lti_ResourcesController extends AdminBaseController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        if (Navigation::hasItem('/course/lti/index')) {
            Navigation::activateItem('/course/lti/index');
        }

        $widget = Sidebar::get()->addWidget(new ActionsWidget());

        $widget->addLink(
            _('LTI-Ressource hinzufügen'),
            $this->url_for('admin/lti/resources/create'),
            Icon::create('add')
        );
    }

    public function index_action(): void
    {
        PageLayout::setTitle(_('LTI-Ressourcen'));

        $this->render_vue_app(
            Studip\VueApp::create('lti/resources/Index')
        );
    }

    public function create_action()
    {
        PageLayout::setTitle(_('LTI-Ressource hinzufügen'));

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

        $deploymentId = Registration::find(Request::get('registration_id'))?->getDefaultDeployment()->id;

        foreach ($this->extractResourcesFromRequest() as $resource) {
            ResourceLink::create([
                ...$resource,
                'deployment_id' => $deploymentId,
                'course_id' => $this->range_id
            ]);
        }

        PageLayout::postSuccess(
            _('Folgende LTI-Ressourcen wurden hinzugefügt.'),
            Request::getArray('title')
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
                    'registrations' => $this->getTransformedRegistrations(RegistrationStatus::all()),
                    'icons' => $this->getStudipIcons()
                ])
        );
    }

    public function update_action(ResourceLink $resourceLink): void
    {
        CSRFProtection::verifyUnsafeRequest();

        $deploymentId = null;
        if (Request::get('registration_id')) {
            $deploymentId = Registration::find(Request::get('registration_id'))?->getDefaultDeployment()->id;
        }

        $resourcesArray = $this->extractResourcesFromRequest();
        if (count($resourcesArray) === 1) {
            $resourceLink->setData([
                ...$resourcesArray[0],
                'deployment_id' => $deploymentId ?? $resourceLink->deployment_id
            ]);

            $resourceLink->store();
        } else {
            foreach ($resourcesArray as $resource) {
                ResourceLink::create([
                    ...$resource,
                    'position' => $resourceLink->position,
                    'course_id' => $resourceLink->course_id,
                    'deployment_id' => $deploymentId ?? $resourceLink->deployment_id
                ]);
            }

            $resourceLink->delete();
        }

        PageLayout::postSuccess(
            _('Folgende LTI-Ressourcen wurden gespeichert.'),
            Request::getArray('title')
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

    private function getTransformedRegistrations(array $status = []): array
    {
        $registrations = Registration::findBySQL(
            "`role`= 'tool' AND `status` IN (:status) AND `range_id` IN (:range_ids) ORDER BY `mkdate`, `name`",
            [
                'status' => [
                    ...$status,
                    RegistrationStatus::Active->value
                ],
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

    private function extractResourcesFromRequest(): array
    {
        $resources = [];

        for ($index = 0; $index < count(Request::getArray('resource_id')); $index++) {
            $resources[] = [
                'resource_id' => Request::getArray('resource_id', $index),
                'title' => Request::getArray('title', $index),
                'description' => Request::getArray('description', $index),
                'custom_parameters' => Request::getArray('custom_parameters', $index),
                'launch_url' => Request::getArray('launch_url', $index),
                'launch_container' => Request::getArray('launch_container', $index) ?? 'window',
                'color' => Request::getArray('color', $index) ?? null,
                'icon' => Request::getArray('icon', $index) ?? null
            ];
        }

        return $resources;
    }

}
