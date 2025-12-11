<?php
namespace LTI;

use AuthenticatedController;
use Icon;
use Navigation;
use PageLayout;
use Request;
use Sidebar;
use ActionsWidget;
use ViewsWidget;

abstract class AdminBaseController extends AuthenticatedController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        $GLOBALS['perm']->check('root');
        Navigation::activateItem('/admin/config/lti');
        PageLayout::setTitle(_('LTI-Registrierungen'));
        $this->role = Request::get('role', 'tool');

        $this->buildSidebar();
    }

    protected function buildSidebar(): void
    {
        // views:
        $viewWidget = new ViewsWidget();

        $viewWidget->addLink(
            _('LTI-Tools'),
            $this->url_for('admin/lti/registrations', ['role' => 'tool']),
        )->setActive($this->role !== 'platform');

        $viewWidget->addLink(
            _('LTI-Platforms'),
            $this->url_for('admin/lti/registrations', ['role' => 'platform'])
        )->setActive($this->role === 'platform');

        Sidebar::Get()->addWidget($viewWidget);

        // actions:
        $actions = new ActionsWidget();
        $actions->addLink(
            _('Neues LTI-Tool registrieren'),
            $this->url_for('admin/lti/registrations/create', ['role' => 'tool']),
            Icon::create('add')
        )->asDialog('width=900;height=650');

        $actions->addLink(
            _('Neues LTI-Platform registrieren'),
            $this->url_for('admin/lti/registrations/create', ['role' => 'platform']),
            Icon::create('add')
        )->asDialog('width=900;height=650');

        $actions->addLink(
            _('Daten zur LTI-Plattform anzeigen'),
            $this->url_for('admin/lti/registrations/platform_data'),
            Icon::create('info')
        )->asDialog('width=900;height=700');

        $actions->addLink(
            _('Daten zur LTI-Tool anzeigen'),
            $this->url_for('admin/lti/registrations/tool_data'),
            Icon::create('info')
        )->asDialog('width=900;height=700');

        Sidebar::get()->addWidget($actions);
    }
}
