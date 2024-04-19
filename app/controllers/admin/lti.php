<?php
/**
 * admin/lti.php - LTI consumer API for Stud.IP
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Elmar Ludwig
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 */

class Admin_LtiController extends AuthenticatedController
{
    /**
     * Callback function being called before an action is executed.
     */
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        $GLOBALS['perm']->check('root');

        Navigation::activateItem('/admin/config/lti');
        PageLayout::setTitle(_('Konfiguration der LTI-Tools'));

        $widget = Sidebar::get()->addWidget(new ActionsWidget());
        $widget->addLink(
            _('Neues LTI-Tool registrieren'),
            $this->url_for('lti/tool/add/global'),
            Icon::create('add')
        )->asDialog();
        $widget->addLink(
            _('Daten zur LTI-Plattform anzeigen'),
            $this->url_for('lti/lti13a/platform_data'),
            Icon::create('info')
        )->asDialog();

        Helpbar::get()->addPlainText('', _('Hier können Sie Verknüpfungen mit externen Tools konfigurieren, sofern diese den LTI-Standard (Version 1.x) unterstützen.'));
    }

    /**
     * Display the list of registered LTI tools.
     */
    public function index_action()
    {
        $this->tools = LtiTool::findAll();
    }
}
