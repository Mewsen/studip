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

        $this->buildSidebar();
    }

    protected function buildSidebar(): void
    {
        // views:
        $viewWidget = new ViewsWidget();

        $viewWidget->addLink(
            _('LTI-Tools'),
            $this->url_for('admin/lti/tools')
        )->setActive(str_contains(Request::path(), '/admin/lti/tools'));

        $viewWidget->addLink(
            _('LTI-Platforms'),
            $this->url_for('admin/lti/platforms')
        )->setActive(str_contains(Request::path(), '/admin/lti/platforms'));

        Sidebar::Get()->addWidget($viewWidget);

        // actions:
        $actions = new ActionsWidget();
        $actions->addLink(
            _('Neues LTI-Tool registrieren'),
            $this->url_for('lti/registration/tools/create'),
            Icon::create('add')
        )->asDialog('width=900;height=650');

        $actions->addLink(
            _('Daten zur LTI-Plattform anzeigen'),
            $this->url_for('lti/auth/platform_data'),
            Icon::create('info')
        )->asDialog('width=900;height=650');

        Sidebar::get()->addWidget($actions);
    }
}
