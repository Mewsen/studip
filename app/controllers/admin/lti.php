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

    /**
     * Display dialog for editing an LTI tool.
     *
     * @param   int $id tool id
     */
    public function edit_action($id = null)
    {
        $this->tool     = new LtiTool($id ?: null);
        $this->platform = \Studip\LTI13a\PlatformManager::getPlatformConfiguration();

        if (Request::isPost()) {
            CSRFProtection::verifyUnsafeRequest();
            $this->tool->name = trim(Request::get('name'));
            $this->tool->launch_url = trim(Request::get('launch_url'));
            $this->tool->oidc_init_url   = trim(Request::get('oidc_init_url'));
            $this->tool->jwks_url        = trim(Request::get('jwks_url'));
            $this->tool->deep_linking_url = trim(Request::get('deep_linking_url'));
            $this->tool->consumer_key = trim(Request::get('consumer_key'));
            $this->tool->consumer_secret = trim(Request::get('consumer_secret'));
            $this->tool->custom_parameters = trim(Request::get('custom_parameters'));
            $this->tool->allow_custom_url = Request::int('allow_custom_url', 0);
            $this->tool->deep_linking = Request::int('deep_linking', 0);
            $this->tool->send_lis_person = Request::int('send_lis_person', 0);
            $this->tool->oauth_signature_method = Request::get('oauth_signature_method', 'sha1');
            $this->tool->lti_version = trim(Request::get('lti_version', '1.3a'));
            $errors = $this->tool->validate();
            if ($errors) {
                PageLayout::postError(
                    _('Die folgenden Daten zum LTI-Tool sind fehlerhaft:'),
                    $errors
                );
                return;
            }

            if ($this->tool->store()) {
                PageLayout::postSuccess(sprintf(
                    _('Einstellungen für "%s" wurden gespeichert.'),
                    htmlReady($this->tool->name)
                ));
            }

            if (Request::isDialog()) {
                $this->response->add_header('X-Dialog-Close', '1');
                $this->render_nothing();
            } else {
                $this->redirect('admin/lti');
            }
        }
    }
}
