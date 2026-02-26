<?php

class Massmail_PermissionsController extends \AuthenticatedController
{

    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        if (!\MassMail\MassMailPermission::has(User::findCurrent()->id, true)) {
            throw new AccessDeniedException();
        }

        Navigation::activateItem('/messaging/massmail/permissions');
    }

    /**
     * Lists all existing permissions.
     * @return void
     * @throws AccessDeniedException
     */
    public function index_action()
    {
        PageLayout::setTitle(_('Berechtigungen für den Nachrichtenversand an Zielgruppen'));

        $this->permissions = \MassMail\MassMailPermission::findBySQL("1");
        usort(
            $this->permissions,
            fn ($a, $b) => strnatcasecmp($a->institute_name, $b->institute_name)
        );

        $sidebar = Sidebar::Get();
        $actions = new ActionsWidget();
        $actions->addLink(
            _('Neue Berechtigung vergeben'),
            $this->url_for('massmail/permissions/edit'),
            Icon::create('add'),
        )->asDialog('size=medium');
        $sidebar->addWidget($actions);

        $this->render_vue_app(
            Studip\VueApp::create('massmail/MassMailPermissions')
        );
    }

    /**
     * Provides a form for entering or editing a massmail permission.
     * @param int $id which permission to edit, create a new one if $id is 0
     * @return void
     * @throws AccessDeniedException
     */
    public function edit_action(int $id = 0)
    {
        $permission = new \MassMail\MassMailPermission($id);

        PageLayout::setTitle(
            $permission->isNew()
                ? _('Berechtigung erstellen')
                : _('Berechtigung bearbeiten')
        );

        $institutes = [];
        foreach (Institute::findAll() as $one) {
            $institutes[$one->id] = $one->name;
        }

        $degrees = [];
        foreach (Degree::findBySQL("1 ORDER BY `name`") as $one) {
            $degrees[$one->id] = $one->name;
        }

        $subjects = [];
        foreach (StudyCourse::findBySQL("1 ORDER BY `name`") as $one) {
            $subjects[$one->id] = $one->name;
        }

        $form = \Studip\Forms\Form::fromSORM(
            $permission,
            [
                'fields' => [
                    'institute_id' => [
                        'type' => 'select',
                        'required' => true,
                        'label' => _('Einrichtung'),
                        'value' => $permission->institute_id,
                        'options' => $institutes
                    ],
                    'min_perm' => [
                        'type' => 'select',
                        'required' => true,
                        'label' => _('Benötigte Rechte'),
                        'value' => $permission->min_perm,
                        'options' => [
                            'admin' => 'admin',
                            'dozent' => 'dozent',
                            'tutor' => 'tutor'
                        ]
                    ],
                    'allowed_degrees' => [
                        'type' => 'checkboxCollection',
                        'collapsable' => true,
                        'label' => _('Erlaubte Abschlüsse'),
                        'value' => $permission->allowed_degrees->pluck('id'),
                        'options' => $degrees
                    ],
                    'allowed_subjects' => [
                        'type' => 'checkboxCollection',
                        'collapsable' => true,
                        'label' => _('Erlaubte Fächer'),
                        'value' => $permission->allowed_subjects->pluck('id'),
                        'options' => $subjects
                    ],
                    'allowed_institutes' => [
                        'type' => 'checkboxCollection',
                        'collapsable' => true,
                        'label' => _('Erlaubte Einrichtungen (außer den eigenen)'),
                        'value' => $permission->allowed_institutes->pluck('id'),
                        'options' => $institutes
                    ]
                ]
            ]
        )->setURL($this->url_for('massmail/permissions/store', $id));

        $this->render_form($form);
    }

    /**
     * Stores permission data by editing an existing or creating a new one.
     * @param int $id the permission to store
     * @return void
     * @throws AccessDeniedException
     */
    public function store_action(int $id = 0)
    {
        CSRFProtection::verifyUnsafeRequest();
        $permission = new \MassMail\MassMailPermission($id);
        $permission->institute_id = Request::option('institute_id');
        $permission->min_perm = Request::get('min_perm');
        $permission->allowed_degrees = Degree::findMany(Request::optionArray('allowed_degrees'));
        $permission->allowed_subjects = StudyCourse::findMany(Request::optionArray('allowed_subjects'));
        $permission->allowed_institutes = Institute::findMany(Request::optionArray('allowed_institutes'));

        if ($permission->store() !== false) {
            PageLayout::postSuccess('Die Daten wurden gespeichert.');
        } else {
            PageLayout::postError('Die Daten konnten nicht gespeichert werden.');
        }

        $this->relocate('massmail/permissions');
    }

    /**
     * Deletes the given permission entry.
     * @param int $id the permission to delete
     * @return void
     * @throws AccessDeniedException
     */
    public function delete_action(int $id)
    {
        $permission = \MassMail\MassMailPermission::find($id);
        if ($permission) {
            if ($permission->delete()) {
                PageLayout::postSuccess(_('Die Berechtigung wurde gelöscht.'));
            } else {
                PageLayout::postError(_('Die Berechtigung konnte nicht gelöscht werden.'));
            }
        } else {
            PageLayout::postError(_('Die Berechtigung wurde nicht gefunden.'));
        }

        $this->relocate('massmail/permissions');
    }

}
