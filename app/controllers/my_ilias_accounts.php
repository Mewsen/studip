<?php
# Lifter007: TODO
# Lifter003: TODO
# Lifter010: TODO
/*
 * my_ilias_accounts.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Arne Schroeder <schroeder@data-quest.de>
 * @copyright   2018 Suchi & Berg GmbH <info@data-quest.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 * @since       4.3
 */
class MyIliasAccountsController extends AuthenticatedController
{
    /**
     * Before filter, set up the page by initializing the session and checking
     * all conditions.
     *
     * @param String $action Name of the action to be invoked
     * @param Array  $args   Arguments to be passed to the action method
     */
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        if (!Config::Get()->ILIAS_INTERFACE_ENABLE ) {
            throw new AccessDeniedException(_('Ilias-Interface ist nicht aktiviert.'));
        } else {
            $this->ilias_active = true;
        }

        PageLayout::setHelpKeyword('Basis.Ilias');
        $this->sidebar = Sidebar::get();
    }

    /**
     * Displays accounts and ilias_interface modules for active user
     */
    public function index_action()
    {
        Navigation::activateItem('/contents/my_ilias_accounts/my_accounts');

        PageLayout::setTitle(_('Meine Lernobjekte und Accounts'));

        $this->ilias_list = [];
        foreach (Config::get()->ILIAS_INTERFACE_SETTINGS as $ilias_index => $ilias_config) {
            if ($ilias_config['is_active']) {
                $this->ilias_list[$ilias_index] = new ConnectedIlias($ilias_index);
                $this->ilias_list[$ilias_index]->checkUser();
                $this->ilias_list[$ilias_index]->soap_client->clearCache();
            }
        }

        $widget = new ActionsWidget();
        foreach($this->ilias_list as $ilias_list_index => $ilias) {
            if ($GLOBALS['perm']->have_perm('autor')) {
                $widget->addLink(
                        sprintf(_('Zur %s-Startseite'), $ilias->getName()),
                        $this->url_for('my_ilias_accounts/redirect/'.$ilias_list_index.'/login'),
                        Icon::create('link-extern'),
                        ['target' => '_blank', 'rel' => 'noopener noreferrer']
                        );
            }
        }
        $this->sidebar->addWidget($widget);
    }

    /**
     * Shows ilias courses for active user
     */
    public function my_courses_action()
    {
        Navigation::activateItem('/contents/my_ilias_accounts/my_courses');

        PageLayout::setTitle(_('Meine ILIAS-Kurse'));

        if (Semester::exists(Request::option('sem_select'))) {
            $this->selected_semester = Request::option('sem_select');
        } else {
            $this->selected_semester = '';
        }

        // set up connected ilias classes
        $this->ilias_list = [];
        foreach (Config::get()->ILIAS_INTERFACE_SETTINGS as $ilias_index => $ilias_config) {
            if ($ilias_config['is_active']) {
                $this->ilias_list[$ilias_index] = new ConnectedIlias($ilias_index);
                $this->ilias_list[$ilias_index]->checkUser();
                $this->ilias_list[$ilias_index]->soap_client->clearCache();
            }
        }

        if (!empty(Config::get()->ILIAS_INTERFACE_BASIC_SETTINGS['show_course_paths'])) {
            // get semesters and set up filter widget
            $semesters = new SimpleCollection(Semester::getAll());
            $semesters = $semesters->orderBy('beginn desc');
            $current_semester = Semester::findCurrent()->id;

            $widget = new SelectWidget(_('Semesterfilter'), $this->url_for('my_ilias_accounts/my_courses'), 'sem_select');
            $widget->setMaxLength(50);

            $sem_entries = [
                $current_semester => _('Aktuelles Semester'),
                ''                => _('Alle Semester')
            ];
            foreach ($sem_entries as $key => $label) {
                $widget->addElement(new SelectElement($key, $label, $this->selected_semester === $key));
            }

            foreach ($semesters as $key => $semester_entry) {
                $widget->addElement(new SelectElement($key, $semester_entry->name, $this->selected_semester === $key));
            }
            $this->sidebar->addWidget($widget);
        }

        $widget = new ActionsWidget();
        foreach ($this->ilias_list as $ilias_list_index => $ilias) {
            if ($GLOBALS['perm']->have_perm('autor')) {
                $widget->addLink(
                    sprintf(_('Zur %s-Startseite'), $ilias->getName()),
                    $this->url_for('my_ilias_accounts/redirect/' . $ilias_list_index . '/login'),
                    Icon::create('link-extern'),
                    ['target' => '_blank', 'rel' => 'noopener noreferrer']
                );

                // fetch connected course ids for user
                $this->connected_courses_list[$ilias_list_index] = $ilias->getConnectedCoursesForUser($ilias->user->studip_id);

                // fetch ilias courses for user
                $member_courses = $ilias->soap_client->getCoursesForUserStatus($ilias->user->id, 1);
                $tutor_courses = $ilias->soap_client->getCoursesForUserStatus($ilias->user->id, 2);
                $admin_courses = $ilias->soap_client->getCoursesForUserStatus($ilias->user->id, 4);
                $this->courses_list[$ilias_list_index] = $member_courses + $tutor_courses + $admin_courses;

                // add paths and studip objects
                foreach ($this->courses_list[$ilias_list_index] as $crs_id => $course_data) {
                    $this->courses_list[$ilias_index][$crs_id]['studip_object'] = '';
                    if (
                        array_key_exists($ilias_list_index, $this->connected_courses_list)
                        && array_key_exists($crs_id, $this->connected_courses_list[$ilias_list_index])
                    ) {
                        $this->courses_list[$ilias_index][$crs_id]['studip_object'] = $this->connected_courses_list[$ilias_list_index][$crs_id];
                    }

                    // filter offline courses for mere members
                    if (
                        !Config::get()->ILIAS_INTERFACE_BASIC_SETTINGS['show_offline']
                        && !$course_data['online']
                        && !in_array($course_data['status'], [2, 4])
                    ) {
                        unset($this->courses_list[$ilias_list_index][$crs_id]);
                    }

                    // filter by semester
                    if (Config::get()->ILIAS_INTERFACE_BASIC_SETTINGS['show_course_paths']) {
                        $this->courses_list[$ilias_list_index][$crs_id]['path'] = $ilias->soap_client->getPath($crs_id);
                        if (
                            $this->selected_semester
                            && !str_contains(
                                $this->courses_list[$ilias_list_index][$crs_id]['path'],
                                Semester::find($this->selected_semester)->name
                            )
                        ) {
                            unset($this->courses_list[$ilias_list_index][$crs_id]);
                        }
                    }
                }
            }
        }
        $this->sidebar->addWidget($widget);
    }

    /**
     * View ILIAS module Details
     * @param $index Index of ILIAS installation
     * @param $module_id module ID
     */
    public function view_object_action($index, $module_id)
    {
        $this->ilias = new ConnectedIlias($index);
        if ($this->ilias->isActive()) {
            $modules = $this->ilias->getUserModules();
            $this->module = $modules[$module_id];
            PageLayout::setTitle($this->module->getTitle());
            $this->ilias_index = $index;
        } else {
            PageLayout::postError(_('Diese ILIAS-Installation ist nicht aktiv.'));
        }
    }

    /**
     * View ILIAS course details
     * @param $index Index of ILIAS installation
     * @param $crs_id course ID
     */
    public function view_course_action($index, $crs_id)
    {
        $this->ilias = new ConnectedIlias($index);
        if ($this->ilias->isActive()) {
            $this->module = $this->ilias->getModule($crs_id);
            $this->module->module_type_name = _('ILIAS-Kurs');
            PageLayout::setTitle($this->module->getTitle());
            $this->ilias_index = $index;
        } else {
            PageLayout::postError(_('Diese ILIAS-Installation ist nicht aktiv.'));
        }
    }

    /**
     * Add module to ILIAS installation
     * @param $index Index of ILIAS installation
     */
    public function add_object_action($index)
    {
        $this->ilias = new ConnectedIlias($index);
        if ($this->ilias->isActive()) {
            $this->ilias_ref_id = $this->ilias->user->getCategory();
            $this->ilias_index = $index;
        } else {
            PageLayout::postError(_('Diese ILIAS-Installation ist nicht aktiv.'));
        }
    }

    /**
     * Set new account for ILIAS installation
     * @param $index Index of ILIAS installation
     */
    public function new_account_action($index)
    {
        $ilias_configs = Config::get()->ILIAS_INTERFACE_SETTINGS;
        if ($ilias_configs[$index]['is_active']) {
            $this->ilias = new ConnectedIlias($index);
            $this->ilias_index = $index;
        }
    }

    /**
     * Change/update account for ILIAS installation
     * @param $index Index of ILIAS installation
     */
    public function change_account_action($index, $mode)
    {
        $ilias_configs = Config::get()->ILIAS_INTERFACE_SETTINGS;
        if ($ilias_configs[$index]['is_active']) {
            $this->ilias = new ConnectedIlias($index);
            $this->ilias_index = $index;
            switch ($mode) {
                case 'update' :
                    // update user account
                    if ($this->ilias->updateUser($GLOBALS['user'])) {
                        PageLayout::postSuccess(_('ILIAS-Account aktualisiert.'));
                    }
                    break;
                case 'add' :
                    // set new user account
                    if ($this->ilias->soap_client->checkPassword(Request::get('ilias_login'), Request::get('ilias_password'))) {
                        // login data valid
                        $user_id = $this->ilias->soap_client->lookupUser(Request::get('ilias_login'));
                        if ($user_id) {
                            $this->ilias->user->setUsername(Request::get('ilias_login'));
                            $this->ilias->user->setPassword('');
                            $this->ilias->user->setId($user_id);
                            $this->ilias->user->setConnection(IliasUser::USER_TYPE_ORIGINAL);
                            PageLayout::postSuccess(_('ILIAS-Account zugeordnet.'));
                            $this->ilias->soap_client->clearCache();
                        }
                    } else {
                        // wrong login
                        PageLayout::postError(_('Login fehlgeschlagen. Die Zuordnung konnte nicht geändert werden.'));
                    }
                    break;
                case 'remove' :
                    $this->ilias->user->unsetConnection();
                    PageLayout::postSuccess(_('Account-Zuordnung entfernt.'));
                    break;
            }
        }
        $this->redirect($this->url_for('my_ilias_accounts/index'));
    }

    /**
     * Administrate account for ILIAS installation
     * @param $user_id studip user id
     * @param $index Index of ILIAS installation
     * @param $mode action type
     */
    public function administrate_account_action($user_id, $index)
    {
        if (!$GLOBALS['perm']->have_perm('root')) {
            throw new AccessDeniedException();
        }

        $ilias_configs = Config::get()->ILIAS_INTERFACE_SETTINGS;
        if ($ilias_configs[$index]['is_active']) {
            $this->ilias = new ConnectedIlias($index);
            $this->ilias_index = $index;
            $this->ilias_login = '';
            $this->matched_user = false;
            $this->external_account_login = '';
            $this->external_account_id = false;
            $this->user_exists = false;
            $this->user = new IliasUser($index, $ilias_configs[$index]['version'], $user_id);

            if (Request::submitted('lookup_account')) {
                $this->ilias_login = trim(Request::option('ilias_login'));
                $this->matched_user = $this->ilias->soap_client->lookupUser($this->ilias_login);
                if (empty($this->matched_user)) {
                    PageLayout::postError(sprintf(_('Es wurde kein Account mit dem Loginnamen "%s" gefunden.'), htmlReady($this->ilias_login)));
                } else {
                    PageLayout::postInfo(sprintf(_('Account "%s" wurde gefunden.'), htmlReady($this->ilias_login)));
                }
            } elseif (Request::submitted('connect_account')) {
                $new_user = $this->ilias->soap_client->getUser(Request::option('ilias_user_id'));
                if ($new_user['usr_id'] && $new_user['login']) {
                    $this->user->id = $new_user['usr_id'];
                    $this->user->login = $new_user['login'];
                    $this->user->setConnection(IliasUser::USER_TYPE_ORIGINAL);
                    PageLayout::postSuccess(_('Account zugeordnet.'));
                }
            } elseif (Request::submitted('disconnect_account')) {
                if ($this->user->unsetConnection(true)) {
                    PageLayout::postSuccess(_('Account-Zuordnung entfernt.'));
                }
            } elseif (Request::submitted('new_account')) {
                $this->ilias->user = new IliasUser($index, $ilias_configs[$index]['version'], $user_id);
                $this->ilias->soap_client->setCachingStatus(false);
                $this->ilias->soap_client->clearCache();
                $this->ilias->newUser();
                PageLayout::postSuccess(_('Account angelegt.'));
            }

            // check if connection is valid / available
            if ($this->user->isConnected()) {
                $existing_user = $this->ilias->soap_client->getUser($this->user->id);
                if ($existing_user && $existing_user['usr_id'] === $this->user->id) {
                    $this->user_exists = true;
                }
            } else {
                $this->external_account_login = $ilias_configs[$index]['user_prefix'] . $this->user->studip_login;
                $this->external_account_id = $this->ilias->soap_client->lookupUser($this->external_account_login);
            }
        }
    }

    /**
     * Redirect to ILIAS installation
     * @param $index Index of ILIAS installation
     */
    public function redirect_action($index, $target, $module_id = '', $module_type = '')
    {
        $ilias_configs = Config::get()->ILIAS_INTERFACE_SETTINGS;
        if ($ilias_configs[$index]['is_active']) {
            $this->ilias = new ConnectedIlias($index);
            $token = $this->ilias->user->getToken();
            $session_id = $this->ilias->soap_client->loginUser($this->ilias->user->getUsername(), $token);
            if ($this->ilias->ilias_config['category_create_on_add_module'] && $GLOBALS['perm']->have_perm($this->ilias->ilias_config['author_perm']) && ($target == 'new') && ! $module_id) {
                $this->ilias->newUserCategory();
                $module_id = $this->ilias->user->category;
            }
            // display error message if session is invalid
            if (! $this->ilias->user->isConnected() && $this->ilias->ilias_config['no_account_updates']) {
                PageLayout::postError(sprintf(
                    _('Sie haben im System %s noch keinen Account. Loggen Sie sich zuerst in %s ein, um ILIAS-Lernobjekte in Stud.IP nutzen zu können.'),
                    htmlReady($this->ilias->getName()),
                    '<a href="'.$this->ilias->getAbsolutePath().'">'.htmlReady($this->ilias->getName()).'</a>'
                ));
            } elseif (!$session_id) {
                PageLayout::postError(
                    sprintf(
                        _('Automatischer Login für %s-Installation (Nutzername %s) fehlgeschlagen.'),
                        htmlReady($this->ilias->getName()),
                        htmlReady($this->ilias->user->getUsername())
                    ),
                    $this->ilias->getError()
                );
            } elseif (($target == 'new') AND ! $module_id) {
                PageLayout::postError(sprintf(_('Keine Kategorie zum Anlegen neuer Lernobjekte in der %s-Installation vorhanden.'),
                        htmlReady($this->ilias->getName())));
            } else {
                // remove client id from session id
                $session_array = explode('::', $session_id);
                $session_id = $session_array[0];

                if (Request::get('ilias_module_type')) $module_type = Request::get('ilias_module_type');

                // build target link
                $parameters = '?sess_id='.$session_id;
                if (!empty($this->ilias->getClientId())) {
                    $parameters .= '&client_id='.$this->ilias->getClientId();
                    if ($target) {
                        $parameters .= '&target='.$target;
                    }
                    if ($module_id) {
                        $parameters .= '&ref_id='.$module_id;
                    }
                    if ($module_type) {
                        $parameters .= '&type='.$module_type;
                    }

                    // refer to ILIAS target file
                    header('Location: '. $this->ilias->getTargetFile() . $parameters);
                    $this->render_nothing();
                }
            }
        }
    }
}
