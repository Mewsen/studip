<?php
namespace Studip\Lti\Controller;

use Icon;
use Context;
use Sidebar;
use LtiToolModule;
use ViewsWidget;
use ActionsWidget;
use AccessDeniedException;
use AuthenticatedController;

abstract class AdminBaseController extends AuthenticatedController
{
    protected ?string $range_id = null;
    protected bool $isModerator = false;
    protected bool $isToolSharingEnabled = false;

    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        $this->range_id = Context::getId();
        $this->isModerator = LtiToolModule::isModerator($this->range_id);

        if (!$this->isModerator) {
            throw new AccessDeniedException();
        }

        $this->isToolSharingEnabled = LtiToolModule::isToolSharingEnabled();
    }

    protected function buildRegistrationsSidebar(): void
    {
        // views:
        $viewWidget = new ViewsWidget();

        $viewWidget->addLink(
            _('LTI-Tools'),
            $this->url_for('admin/lti/registrations', ['role' => 'tool']),
        )->setActive($this->ltiRole !== 'platform');

        if ($this->isToolSharingEnabled) {
            $viewWidget->addLink(
                _('LTI-Platforms'),
                $this->url_for('admin/lti/registrations', ['role' => 'platform'])
            )->setActive($this->ltiRole === 'platform');
        }

        Sidebar::Get()->addWidget($viewWidget);

        // actions:
        $actions = new ActionsWidget();
        $actions->addLink(
            _('Neues LTI-Tool registrieren'),
            $this->url_for('admin/lti/registrations/create', ['role' => 'tool']),
            Icon::create('add')
        )->asDialog('width=900;height=700');

        if ($this->isToolSharingEnabled) {
            $actions->addLink(
                _('Neues LTI-Platform registrieren'),
                $this->url_for('admin/lti/registrations/create', ['role' => 'platform']),
                Icon::create('add')
            )->asDialog('width=900;height=700');
        }

        $actions->addLink(
            _('Daten zur LTI-Plattform anzeigen'),
            $this->url_for('admin/lti/registrations/platform_data'),
            Icon::create('info')
        )->asDialog('width=900;height=700');

        if ($this->isToolSharingEnabled) {
            $actions->addLink(
                _('Daten zur LTI-Tool anzeigen'),
                $this->url_for('admin/lti/registrations/tool_data'),
                Icon::create('info')
            )->asDialog('width=900;height=700');
        }

        Sidebar::get()->addWidget($actions);
    }

    protected function buildPublicationsSidebar(): void
    {
        if($this->isToolSharingEnabled) {
            // actions:
            $actions = new ActionsWidget();
            $actions->addLink(
                _('Neue Veröffentlichung anlegen'),
                $this->url_for('admin/lti/publications/create'),
                Icon::create('add')
            )->asDialog('width=700');

            Sidebar::get()->addWidget($actions);
        }
    }

}
