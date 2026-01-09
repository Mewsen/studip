<?php
namespace LTI;

use AccessDeniedException;
use AuthenticatedController;
use Config;
use Context;
use Icon;
use LtiToolModule;
use Sidebar;
use ActionsWidget;
use User;
use ViewsWidget;

abstract class AdminBaseController extends AuthenticatedController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        $this->range_id = Context::getId();

        if (!LtiToolModule::isModerator($this->range_id)) {
            throw new AccessDeniedException();
        }

        $this->isToolSharingEnabled = Config::get()->ENABLE_SHARING_COURSES_AS_LTI_TOOLS;
    }

    protected function buildRegistrationsSidebar(): void
    {
        // views:
        $viewWidget = new ViewsWidget();

        $viewWidget->addLink(
            _('LTI-Tools'),
            $this->url_for('admin/lti/registrations', ['role' => 'tool']),
        )->setActive($this->role !== 'platform');

        if ($this->isToolSharingEnabled) {
            $viewWidget->addLink(
                _('LTI-Platforms'),
                $this->url_for('admin/lti/registrations', ['role' => 'platform'])
            )->setActive($this->role === 'platform');
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
