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
     * Adds workgroup
     */
    public function add_workgroup_action($ilias_index) 
    {
        $this->ilias = new ConnectedIlias($ilias_index);
        $this->ilias_workgroup_name = sprintf(_('Arbeitsbereich von %s'), User::findCurrent()->getFullName());

        if (
            empty($this->ilias->ilias_config['workgroup_perm'])
            || !$GLOBALS['perm']->have_perm($this->ilias->ilias_config['workgroup_perm'])
        ) {
            throw new AccessDeniedException();
        }

        if (Request::submitted('add_workgroup')) {
            $this->ilias_workgroup_name = trim(Request::get('ilias_workgroup_name'));
            if (
                empty($this->ilias_workgroup_name)
                || mb_strlen($this->ilias_workgroup_name) < 3
            ) {
                PageLayout::postError(_('Der Name des Arbeitsbereichs ist zu kurz.'));
            } elseif (
                $this->ilias->isActive()
                && !empty($this->ilias->ilias_config['workgroup_category'])
                && !empty($this->ilias->ilias_config['workgroup_role'])
            ) {
                //create workgroup category in ILIAS
                $object_data = [
                    'title' => $this->ilias_workgroup_name,
                    'description' => '',
                    'type' => 'cat', 
                    'owner' => $this->ilias->soap_client->lookupUser($this->ilias_config['admin']),
                ];
                $workgroup_cat = $this->ilias->soap_client->addObject($object_data, $this->ilias->ilias_config['workgroup_category']);

                if (!empty($workgroup_cat)) {
                    // create local role from template
                    $role_data = [
                        'title' => "studip_workgroup_ref_" . $workgroup_cat,
                        'description' => sprintf(_('Arbeitsbereichsrolle für ref_id %s, angelegt von %s'), $workgroup_cat, User::findCurrent()->getFullName()),
                    ];
                    $role_id = $this->ilias->soap_client->addRoleFromTemplate($role_data, $workgroup_cat, $this->ilias->ilias_config['workgroup_role']);

                    if (!empty($role_id)) {
                        //add current user to new role entry
                        $this->ilias->soap_client->addUserRoleEntry($this->ilias->user->getId(), $role_id);
                        
                        // delete permissions for all global roles (User, Guest, Anonymous) for this category
                        foreach ($this->ilias->global_roles as $role) {
                            $this->ilias->soap_client->revokePermissions($role, $workgroup_cat);
                        }

                        PageLayout::postSuccess(sprintf(_('Der Arbeitsbereich mit dem Namen "%s" wurde angelegt.'), $this->ilias_workgroup_name));
                    } else {
                        PageLayout::postError(sprintf(_('Die Mitglieder-Rolle für den Arbeitsbereich "%s" konnte nicht angelegt werden.'), $this->ilias_workgroup_name));
                    }
                } else {
                    PageLayout::postError(sprintf(_('Der Arbeitsbereich "%s" konnte nicht angelegt werden.'), $this->ilias_workgroup_name));
                }
            } else {
                PageLayout::postError(sprintf(_('Die ILIAS-Installation %s ist nicht aktiv.'), $this->ilias->ilias_config['name']));
            }
            $this->redirect($this->url_for('my_ilias_accounts/my_courses'));
        }
    }

    /**
     * Sends workgroup requests to given user(s)
     */
    public function request_workgroup_member_action($ilias_index, $workgroup_id) 
    {
        $this->ilias = new ConnectedIlias($ilias_index);

        if ($this->ilias->isActive()) {
            $this->ilias_index = $ilias_index;
            // Get selected persons.
            $mp = MultiPersonSearch::load('add_ilias_workgroup_member' . $workgroup_id);

            $requests_sent = 0;
            $workgroup = $this->ilias->getWorkgroup($workgroup_id);

            if (!empty($workgroup)) {
                $messaging = new messaging();
                foreach ($mp->getAddedUsers() as $user_id) {
                    $user = new IliasUser($this->ilias_index, $this->ilias_int_version, $user_id);
                    if (!$user->isConnected()) {
                        PageLayout::postInfo(sprintf(
                            _('%s wurde übersprungen, da kein ILIAS-Account verknüpft ist.'), 
                            htmlReady($user->getName())
                        ));
                    } elseif (!empty($workgroup['members'][$user_id])) {
                        PageLayout::postInfo(sprintf(
                            _('%s wurde übersprungen, da bereits Mitglied des Arbeitsbereichs.'), 
                            htmlReady($user->getName())
                        ));
                    } else {
                        $message_title = sprintf(_('Mitgliedschaftsanfrage für ILIAS-Arbeitsbereich "%s"'), $workgroup['title']);
                        $message_body = sprintf(_('Sie haben eine Mitgliedschafts-Anfrage für den ILIAS-Arbeitsbereich "%s" erhalten.'), $workgroup['title'])."\n\n";
                        $message_body .= _('Um dem Arbeitsbereich beizutreten, klicken Sie bitte auf den folgenden Link:')."\n\n"; 
                        $message_body .= '['. _('ILIAS-Arbeitsbereich hinzufügen') . ']' . $this->url_for('my_ilias_accounts/accept_workgroup_request', $this->ilias_index, $workgroup_id)."\n\n"; 
                        $message_body .= _('Diese Anfrage ist für eine Woche ab Erhalt der Nachricht gültig.');

                        $recipients = [$user->studip_login];
                        $messaging->insert_message(
                            $message_body,
                            $recipients,
                            '____%system%____',
                            '',
                            '',
                            '',
                            null,
                            $message_title
                        );
                        $requests_sent++;
                        $this->ilias->addWorkgroupRequest($user_id, $workgroup_id);
                    }
                }
                if ($requests_sent === 1) {
                    PageLayout::postInfo(sprintf(_('Es wurde eine Anfrage für den Arbeitsbereich "%s" verschickt.'), $workgroup['title']));
                } elseif ($requests_sent >= 0) {
                    PageLayout::postInfo(sprintf(_('Es wurden %s Anfragen für den Arbeitsbereich "%s" verschickt.'), $requests_sent, $workgroup['title']));
                }
        } else {
                PageLayout::postError(sprintf(_('Arbeitsbereich %s wurde nicht gefunden oder hat keine lokale Rolle.'), htmlReady($workgroup_id)));
            }
        } else {
            PageLayout::postError(_('Diese ILIAS-Installation ist nicht aktiv.'));
        }
        $this->redirect($this->url_for('my_ilias_accounts/my_courses'));
    }

    /**
     * Accepts workgroup request for current user
     */
    public function accept_workgroup_request_action($ilias_index, $workgroup_id) 
    {
        $this->ilias = new ConnectedIlias($ilias_index);

        if ($this->ilias->isActive()) {
            $this->ilias_index = $ilias_index;

            if ($this->ilias->user->isConnected() && $this->ilias->user->hasWorkgroupRequest($workgroup_id)) {
                $this->ilias->resolveWorkgroupRequest($this->ilias->user->studip_id, $workgroup_id, true);
                $workgroup = $this->ilias->getWorkgroup($workgroup_id);

                if (!empty($this->ilias->getError())) {
                    foreach ($this->ilias->getError() as $error) {
                        PageLayout::postError(htmlReady($error));
                    }
                } else {
                    PageLayout::postSuccess(sprintf(_('Sie wurden in den Arbeitsbereich "%s" eingetragen.'), htmlReady($workgroup['title'])));
                }
            } else {
                PageLayout::postError(_('Der Arbeitsbereich wurde nicht gefunden oder die Anfrage ist nicht mehr gültig.'));
            }
        } else {
            PageLayout::postError(_('Diese ILIAS-Installation ist nicht aktiv.'));
        }
        $this->redirect($this->url_for('my_ilias_accounts/my_courses'));
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
        $this->add_member_search = null;
        $this->courses_list = [];
        $this->workgroups_list = [];
    
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
                    $this->courses_list[$ilias_list_index][$crs_id]['studip_object'] = '';
                    if (
                        array_key_exists($ilias_list_index, $this->connected_courses_list)
                        && array_key_exists($crs_id, $this->connected_courses_list[$ilias_list_index])
                    ) {
                        $this->courses_list[$ilias_list_index][$crs_id]['studip_object'] = $this->connected_courses_list[$ilias_list_index][$crs_id];
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
                    if (!empty(Config::get()->ILIAS_INTERFACE_BASIC_SETTINGS['show_course_paths'])) {
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

                if ($ilias->ilias_config['workgroup_category']) {
                    // Prepare search object for MultiPersonSearch.
                    if (empty($this->add_member_search)) {
                        $this->add_member_search = new PermissionSearch(
                            'user',
                            _('Personen suchen'),
                            'user_id',
                            [
                                'permission' => ['tutor', 'dozent'],
                                'exclude_user' => []
                            ]
                        );
                    }

                    $this->workgroups_list[$ilias_list_index] = $ilias->getUserWorkgroups($GLOBALS['perm']->have_perm('root'));

                    $this->add_workgroups_perm[$ilias_list_index] = 
                        !empty($ilias->ilias_config['workgroup_category'])
                        && !empty($ilias->ilias_config['workgroup_role'])
                        && !empty($ilias->ilias_config['workgroup_perm']) 
                        && $GLOBALS['perm']->have_perm($ilias->ilias_config['workgroup_perm']);
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
     * View ILIAS course details
     * @param $index Index of ILIAS installation
     * @param $crs_id course ID
     */
    public function view_workgroup_action($index, $workgroup_id)
    {
        $this->ilias = new ConnectedIlias($index);
        if ($this->ilias->isActive()) {
            $this->workgroup_data = $this->ilias->getWorkgroup($workgroup_id);
            $this->module = new IliasModule($workgroup_id, $this->workgroup_data, $index, $this->ilias->ilias_int_version);
            $this->module->module_type_name = _('ILIAS-Arbeitsbereich');
            $this->module->icon_file = 'community';
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
                $this->ilias_login = trim(Request::get('ilias_login'));
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
                    $this->redirect($this->ilias->getTargetFile() . $parameters);
                }
            }
        }
    }
}
