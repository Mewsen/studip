<?php
use MassWidget\MassWidget;
use MassWidget\MassWidgetFilter;
use Studip\Forms\Form;

/**
 * start.php - start page controller
 *
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author   André Klaßen <klassen@elan-ev.de>
 * @author   Nadine Werner <nadine.werner@uni-osnabrueck.de>
 * @license  http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category Stud.IP
 * @since    3.1
 */
class StartController extends AuthenticatedController
{
    /**
     * Callback function being called before an action is executed.
     */
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        Navigation::activateItem('/start');
        PageLayout::setTabNavigation(NULL); // disable display of tabs
        PageLayout::setHelpKeyword('Basis.Startseite'); // set keyword for new help
        PageLayout::setTitle(_('Startseite'));
    }

    /**
     * Entry point of the controller that displays the start page of Stud.IP
     *
     * @param string $action
     * @param string $widgetId
     *
     * @return void
     */
    public function index_action($action = false, $widgetId = null)
    {
        $this->syncMassWidgets();

        $plugin_manager = PluginManager::getInstance();
        $widgets = WidgetUser::getWidgets($GLOBALS['user']->id);
        $this->columns = [[], []];

        foreach ($widgets as $col => $list) {
            foreach ($list as $plugin_id) {
                $plugin = $plugin_manager->getPluginById($plugin_id);

                if ($plugin) {
                    $this->columns[$col][] = $plugin;
                }
            }
        }

        $this->widget_layout = $this->get_template_factory()->open('start/_widget.php');

        $sidebar = Sidebar::get();

        $nav = $sidebar->addWidget(new NavigationWidget());
        $nav->setTitle(_('Sprungmarken'));
        $nav->setId('navigation-layer-3');
        foreach (array_merge(...$this->columns) as $widget) {
            $nav->addLink(
                $widget->getPluginName(),
                $this->url_for("start#widget-" . $widget->getPluginId())
            );
        }

        // Show action to add widget only if not all widgets have already been added.
        $actions = $sidebar->addWidget(new ActionsWidget());

        if ($this->getAvailableWidgets($GLOBALS['user']->id)) {
            $actions->addLink(
                _('Widgets hinzufügen'),
                $this->url_for('start/add'),
                Icon::create('add')
            )->asDialog();
        }

        $actions->addLink(
            _('Standard wiederherstellen'),
            $this->url_for('start/reset'),
            Icon::create('accept'),
            ['data-confirm' => _('Sollen wirklich alle Widgets auf den Standard zurückgesetzt werden?')]
        );

        // Root may set initial positions
        if ($GLOBALS['perm']->have_perm('root')) {
            $massWidgetActions = $sidebar->addWidget(new ActionsWidget());
            $massWidgetActions->setTitle(_('Widgets für Zielgruppen'));

            $massWidgetActions->addLink(
                _('Regel hinzufügen'),
                $this->url_for('start/masswidget_edit'),
                Icon::create('add')
            )->asDialog();

            $massWidgetActions->addLink(
                _('Regel-Übersicht'),
                $this->url_for('start/masswidget_index'),
                Icon::create('view-wall')
            )->asDialog();


            $settings = $sidebar->addWidget(new ActionsWidget());
            $settings->setTitle(_('Einstellungen'));
            $settings->addElement(new WidgetElement(_('Standard-Startseite bearbeiten:')));
            foreach ($GLOBALS['perm']->permissions as $permission => $useless) {
                $settings->addLink(
                    ucfirst($permission),
                    $this->url_for("start/edit_defaults/{$permission}"),
                    Icon::create('link-intern')
                )->asDialog();
            }
        }

        $this->messages = [];

        if (User::findCurrent()->perms == 'user') {
            $this->messages[] = MessageBox::info(
                _('Sie haben noch nicht auf Ihre Bestätigungsmail geantwortet.'),
                [
                    _('Bitte holen Sie dies nach, um Stud.IP Funktionen wie das Belegen von Veranstaltungen nutzen zu können.'),
                    sprintf(_('Bei Problemen wenden Sie sich an: %s'), '<a href="mailto:'.$GLOBALS['UNI_CONTACT'].'">'.$GLOBALS['UNI_CONTACT'].'</a>')
                ]
            );

            $details = Studip\LinkButton::create(
                _('Bestätigungsmail erneut verschicken'),
                $this->resend_validation_mailURL()
            );

            if (
                !StudipAuthAbstract::CheckField('auth_user_md5.Email', User::findCurrent()->auth_plugin)
                && !LockRules::check(User::findCurrent()->id, 'email')
            ) {
                $details .= ' ';
                $details .= Studip\LinkButton::create(
                    _('E-Mail-Adresse ändern'),
                    $this->edit_mail_addressURL(),
                    [
                        'data-dialog' => 'size=auto',
                        'title'       => _('E-Mail-Adresse')
                    ]
                );
            }
            $this->messages[] = MessageBox::info(
                sprintf(
                    _('Haben Sie die Bestätigungsmail an Ihre Adresse "%s" nicht erhalten?'),
                    htmlReady(User::findCurrent()->email)
                ),
                [$details]
            );
        }
    }

    /**
     * Fetches all widgets that are not already in use.
     *
     * @param string $user_id the user to check
     *
     * @return array available widgets
     */
    private function getAvailableWidgets($user_id)
    {
        $all_widgets = PluginEngine::getPlugins(PortalPlugin::class);
        $user_widgets = WidgetUser::getWidgets($user_id);
        $used_widgets = array_merge(...$user_widgets);
        $available = [];

        foreach ($all_widgets as $widget) {
            if (!in_array($widget->getPluginId(), $used_widgets)) {
                $available[] = $widget;
            }
        }

        return $available;
    }

    /**
     *  This action adds one or more new widgets to the start page
     *
     * @return void
     */
    public function add_action()
    {
        PageLayout::setTitle(_('Widgets hinzufügen'));

        if (Request::isPost()) {
            $ticket   = Request::get('studip_ticket');
            $widgets  = Request::intArray('widget_id');
            $position = Request::int('position');

            $post_url = '';
            if (check_ticket($ticket)) {
                foreach ($widgets as $widget) {
                    WidgetUser::addWidget($GLOBALS['user']->id, $widget);
                    if (!$post_url) {
                        $post_url = '#widget-' . $widget;
                    }
                }
            }
            $this->redirect('start' . $post_url);
        }
        $this->widgets = $this->getAvailableWidgets($GLOBALS['user']->id);
    }


    /**
     * Edit the default startpage configuration for users by permissions
     *
     * @param string $permission
     *
     * @throws InvalidArgumentException
     */
    public function edit_defaults_action($permission)
    {
        if (!in_array($permission, array_keys($GLOBALS['perm']->permissions))) {
            throw new InvalidArgumentException('There is no such permission!');
        }

        PageLayout::setTitle(sprintf(_('Standard-Startseite für "%s" bearbeiten'), ucfirst($permission)));

        $this->widgets = PluginEngine::getPlugins(PortalPlugin::class);
        $this->initial_widgets = WidgetDefault::getWidgets($permission);
        $this->permission = $permission;
    }

    /**
     * Store the edited default startpage configuration for users by permissions
     *
     * @param string $permission
     *
     * @throws InvalidArgumentException
     */
    public function update_defaults_action($permission)
    {
        $GLOBALS['perm']->check('root');

        if (!in_array($permission, array_keys($GLOBALS['perm']->permissions))) {
            throw new InvalidArgumentException('There is no such permission!');
        }

        $widgets = [Request::getArray('left'), Request::getArray('right')];

        WidgetDefault::deleteBySQL('perm = ?', [$permission]);

        foreach ($widgets as $col => $list) {
            foreach ($list as $plugin_id => $position) {
                WidgetDefault::create([
                    'pluginid' => $plugin_id,
                    'col'      => $col,
                    'position' => $position,
                    'perm'     => $permission
                ]);
            }
        }

        $this->render_nothing();
    }

    /**
     *  This actions removes a new widget from the start page
     *
     * @param string $widgetId
     * @param string $approveDelete
     * @param string $studipticket
     */
    public function delete_action($id)
    {
        $plugin_manager = PluginManager::getInstance();
        $plugin_info = $plugin_manager->getPluginById($id);
        $name = $plugin_info->getPluginName();

        if (Request::isPost()) {
            if (Request::submitted('yes')) {
                if (WidgetUser::removeWidget($GLOBALS['user']->id, $id)) {
                    $message = sprintf(
                        _('Widget "%s" wurde entfernt.'),
                        htmlReady($name)
                    );
                    PageLayout::postSuccess($message);
                } else {
                    $message = sprintf(
                        _('Widget "%s" konnte nicht entfernt werden.'),
                        htmlReady($name)
                    );
                    PageLayout::postError($message);
                }
            }
        } else {
            PageLayout::postQuestion(
                sprintf(
                    _('Sind Sie sicher, dass Sie das Widget "%s" von der Startseite entfernen möchten?'),
                    htmlReady($name)
                ),
                $this->url_for("start/delete/{$id}")
            );
        }
        $this->redirect('start');
    }

    /**
     * Resets widget to initial default state.
     */
    public function reset_action()
    {
        WidgetUser::deleteBySQL('range_id = ?', [$GLOBALS['user']->id]);

        $message = _('Die Widgets wurden auf die Standardkonfiguration zurückgesetzt.');
        PageLayout::postSuccess($message);
        $this->redirect('start');
    }

    /**
     *  Action to store the widget placements
     */
    public function storeNewOrder_action(): void
    {
        if (!Request::isPost()) {
            throw new MethodNotAllowedException();
        }

        $lanes = Request::getArray('lanes');

        WidgetUser::setInitialWidgets($GLOBALS['user']->id);

        foreach ($lanes as $column => $list) {
            foreach ($list as $position => $plugin_id) {
                $widget = WidgetUser::findOneBySQL('pluginid = ? AND range_id = ?', [$plugin_id, $GLOBALS['user']->id]);
                $widget->position = $position;
                $widget->col = $column;
                $widget->store();
            }
        }

        $this->render_nothing();
    }

    /**
     * Resend the validation mail for the current user
     *
     * @return void
     */
    public function resend_validation_mail_action()
    {
        if ($GLOBALS['perm']->get_perm() === 'user') {
            auth()->sendValidationMail();
            PageLayout::postSuccess(
                _('Die Bestätigungsmail wurde erneut verschickt.')
            );
        }

        $this->redirect('start');
    }

    /**
     * Show form to change the mail-address for the validation mail
     *
     * @return void
     */
    public function edit_mail_address_action()
    {
        // only allow editing of mail-address here if user has not yet validated
        if ($GLOBALS['perm']->get_perm() !== 'user') {
            $this->redirect('start');
            return;
        }

        $this->restricted = StudipAuthAbstract::CheckField('auth_user_md5.Email', $GLOBALS['user']->auth_plugin)
                         && LockRules::check($GLOBALS['user']->id, 'email');
        $this->email = $GLOBALS['user']->Email;
    }

    /**
     * Change the mail-address and resend validation mail
     *
     * @return void
     */
    public function change_mail_address_action()
    {
        $email1 = Request::get('email1');
        $email2 = Request::get('email2');
        if ($GLOBALS['perm']->get_perm() == 'user') {

            if($email1 != $email2) {
                PageLayout::postError(_('Die Wiederholung der E-Mail-Adresse stimmt nicht mit Ihrer Eingabe überein.'));
                $this->redirect('start/edit_mail_address');
                return;
            }
            $user = \User::findCurrent();
            $user->Email = $email1;
            $user->store();

            auth()->sendValidationMail($user);
            PageLayout::postMessage(MessageBox::success(
                _('Ihre Mailadresse wurde geändert und die Bestätigungsmail erneut verschickt.')
            ));
        }

        $this->relocate('start');
    }

    public function masswidget_index_action()
    {
        $GLOBALS['perm']->check('root');

        $this->massWidgets = MassWidget::findBySQL('1');
    }

    public function masswidget_edit_action(?MassWidget $massWidget = null)
    {
        $GLOBALS['perm']->check('root');

        PageLayout::setTitle(_('Widget hinzufügen'));

        if (Request::isPost() && !$massWidget->isNew()) {
            CSRFProtection::verifyUnsafeRequest();

            if (
                Request::get('target') !== $massWidget->target
                || Request::int('plugin_id') !== $massWidget->plugin_id
            ) {
                $massWidget->deleteUserWidgets();
            }
        }

        // SearchType needed for course selection
        $courseSearch = new StandardSearch('Seminar_id');

        $widgets = PluginEngine::getPlugins(PortalPlugin::class);

        $availableWidgets = [];
        foreach ($widgets as $widget) {
            $availableWidgets[$widget->getPluginId()] = htmlReady($widget->getPluginName());
        }

        $semesters = [];
        foreach (array_reverse(Semester::getAll()) as $one) {
            $semesters[$one->id] = $one->name;
        }

        $form = Form::fromSORM(
            $massWidget,
            [
                'legend' => _('Zielgruppe'),
                'collapsed' => false,
                'collapsable' => false,
                'fields' => [
                    'target' => [
                        'type' => 'select',
                        'required' => true,
                        'label' => _('Zielgruppe'),
                        'value' => $massWidget->target ?? 'all',
                        'options' => MassWidget::getTargets()
                    ],
                    'student_filters' => [
                        'type' => 'userFilter',
                        'label' => _('Filterauswahl'),
                        'if' => 'target === "students"',
                        'context' => '',
                        'target' => 'students',
                        'store' => function($value, $input) {
                            if ($input->getContextObject()->target === 'students') {
                                $input->getContextObject()->filters = $this->buildMassWidgetFilters($value);
                            }
                        }
                    ],
                    'employee_filters' => [
                        'type' => 'userFilter',
                        'label' => _('Filterauswahl'),
                        'if' => 'target === "employees"',
                        'context' => '',
                        'target' => 'employees',
                        'store' => function($value, $input) {
                            if ($input->getContextObject()->target === 'employees') {
                                $input->getContextObject()->filters = $this->buildMassWidgetFilters($value);
                            }
                        }
                    ],
                    'semester' => [
                        'type' => 'select',
                        'label' => _('Semester'),
                        'value' => $massWidget->settings['semester'] ?? \Semester::findDefault()->id,
                        'if' => 'target === "lecturers"',
                        'options' => $semesters,
                        'store' => function($value, $input) {
                            if ($input->getContextObject()->target === 'lecturers') {
                                $input->getContextObject()->settings = ['semester' => $value];
                            }
                        }
                    ],
                    'courses' => [
                        'type' => 'quicksearchList',
                        'label' => _('Veranstaltungen'),
                        'value' => json_encode($massWidget->settings?->getArrayCopy()['courses'] ?? []),
                        'if' => 'target === "courses"',
                        'searchtype' => $courseSearch,
                        'store' => function($value, $input) {
                            if ($input->getContextObject()->target === 'courses') {
                                $input->getContextObject()->settings = [];
                                $input->getContextObject()->settings['courses'] = \Course::findAndMapMany(
                                    function ($course) {
                                        return ['id' => $course->id, 'name' => $course->getFullname()];
                                    },
                                    json_decode($value, true)
                                );
                            }
                        }
                    ],
                    'course_perm' => [
                        'type' => 'select',
                        'label' => _('Rechtestufe'),
                        'value' => $massWidget->settings['perm'] ?? 'autor',
                        'if' => 'target === "courses"',
                        'options' => [
                            'dozent' => get_title_for_status('dozent', 2, 1),
                            'tutor' => get_title_for_status('tutor', 2, 1),
                            'autor' => get_title_for_status('autor', 2, 1),
                            'user' => get_title_for_status('user', 2, 1),
                        ],
                        'store' => function($value, $input) {
                            if ($input->getContextObject()->target === 'courses') {
                                $input->getContextObject()->settings['perm'] = $value;
                            }
                        }
                    ],
                    'manual_usernames' => [
                        'type' => 'textarea',
                        'label' => _('Liste von Benutzernamen (durch Zeilenumbruch getrennt)'),
                        'if' => 'target === "usernames"',
                        'value' => $massWidget->settings['usernames'] ?? '',
                        'store' => function($value, $input) {
                            if ($input->getContextObject()->target === 'usernames') {
                                $input->getContextObject()->settings = [];
                                $input->getContextObject()->settings['usernames'] = $value;
                            }
                        }
                    ],
                ]
            ],
            $this->url_for('start')
        )->addSORM($massWidget, [
            'legend' => _('Widget'),
            'collapsable' => false,
            'collapsed' => false,
            'fields' => [
                'author_id' => [
                    'type' => 'hidden',
                    'value' => User::findCurrent()->id
                ],
                'name' => [
                    'type' => 'text',
                    'required' => true,
                    'label' => _('Name'),
                    'value' => $massWidget->name
                ],
                'plugin_id' => [
                    'type' => 'select',
                    'required' => true,
                    'label' => _('Widget'),
                    'value' => $massWidget->plugin_id ?? '0',
                    'options' => $availableWidgets
                ],
                'col' => [
                    'if' => 'plugin_id > 0',
                    'value' => $massWidget->col ?? '0',
                    'label' => _('Spalte'),
                    'type' => 'radio',
                    'orientation' => 'vertical',
                    'options' => [
                        '0' => _('Links'),
                        '1' => _('Rechts'),
                    ],
                ],
                'row' => [
                    'if' => 'plugin_id > 0',
                    'value' =>  $massWidget->row ?? '0',
                    'type' => 'radio',
                    'label' => _('Zeile'),
                    'orientation' => 'vertical',
                    'default' => '0',
                    'options' => [
                        '0' => _('Oben'),
                        '1' => _('Unten'),
                    ],
                ]
            ]
        ])
        ->setSuccessMessage(_('Die Regel für das Widget wurde gespeichert.'))
        ->autoStore();

        $this->render_form($form);
    }

    public function masswidget_delete_action(MassWidget $massWidget)
    {
        CSRFProtection::verifyUnsafeRequest();

        $GLOBALS['perm']->check('root');

        $massWidget->deleteUserWidgets()->delete();

        PageLayout::postSuccess(_('Die Regel für das Widget wurde gelöscht.'));

        $this->redirect($this->url_for('start'));
    }

    public function syncMassWidgets(): void
    {
        $massWidgets = MassWidget::findBySQL('1');

        if (count($massWidgets) > 0) {
            WidgetUser::setInitialWidgets(User::findCurrent()->id);
        }

        foreach ($massWidgets as $massWidget) {
            $recipientIds = $massWidget->getTargetUserIds();

            if (in_array(User::findCurrent()->id, $recipientIds)) {
                $userWidget = WidgetUser::findOneBySQL(
                    'pluginid = :plugin_id AND range_id = :user_id',
                    ['plugin_id' => $massWidget->plugin_id, 'user_id' => User::findCurrent()->id]
                );

                if (!$userWidget) {
                    $userWidget = new WidgetUser();
                    $userWidget->range_id = User::findCurrent()->id;
                    $userWidget->pluginid = $massWidget->plugin_id;
                }

                if ($userWidget->isNew() || $massWidget->chdate > $userWidget->chdate) {
                    if ((int) $massWidget->row === 0) {
                        $minRow = DBManager::get()->fetchColumn(
                            "SELECT MIN(`position`) - 1 FROM `widget_user` WHERE `range_id` = ?",
                            [User::findCurrent()->id]
                        );
                        $userWidget->position = $minRow;
                    } else {
                        $maxRow = DBManager::get()->fetchColumn(
                            "SELECT MAX(`position`) + 1 FROM `widget_user` WHERE `range_id` = ?",
                            [User::findCurrent()->id]
                        );
                        $userWidget->position = $maxRow;
                    }

                    $userWidget->is_active = 1;
                    $userWidget->col = $massWidget->col ?? 0;
                    $userWidget->store();
                }
            }
        }
    }

    private function buildMassWidgetFilters(array $value): array
    {
        $filters = [];

        foreach ($value as $one) {
            $filter = new UserFilter($one['id'] ?? '');
            $filter->fields = [];

            foreach ($one['attributes']['fields'] as $field) {
                $className = $field['attributes']['type'];

                if (!is_a($className, UserFilterField::class, true)) {
                    throw new InvalidArgumentException('Only user filters allowed');
                }

                $f = new $className();

                if (!empty($field['id'])) {
                    $f->setId($field['id']);
                }

                $f->setCompareOperator($field['attributes']['compare-operator']);
                $f->setValue($field['attributes']['value']);

                $filter->addField($f);
            }

            $filter->store();

            $connection = new MassWidgetFilter();
            $connection->filter_id = $filter->getId();

            $filters[] = $connection;
        }

        return $filters;
    }
}
