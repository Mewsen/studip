<?php

class Massmail_SettingsController extends \AuthenticatedController
{

    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        if (!\MassMail\MassMailPermission::has(User::findCurrent()->id, true)) {
            throw new AccessDeniedException();
        }

        Navigation::activateItem('/messaging/massmail/settings');
    }

    /**
     * Lists all existing permissions.
     * @return void
     * @throws AccessDeniedException
     */
    public function index_action()
    {
        PageLayout::setTitle(_('Einstellungen für den Nachrichtenversand an Zielgruppen'));

        $categories = [];
        foreach (SemClass::getClasses() as $class) {
            $categories[$class['id']] = $class['name'];
        }

        $form = \Studip\Forms\Form::create();
        $form->setURL($this->url_for('massmail/settings/store'));
        $config = new \Studip\Forms\Fieldset(_('Konfiguration'));
        $config->addInput(
            new \Studip\Forms\NumberInput(
                'cleanup',
                _('Anzahl Tage, nach denen bereits verschickte Nachrichten gelöscht werden (0 bedeutet nie)'),
                Config::get()->MASSMAIL_GC_DAYS,
                ['min' => 0]
            )
        );
        $form->addPart($config);

        $form->addInput(
            new \Studip\Forms\CheckboxCollectionInput(
                'categories',
                _('Veranstaltungskategorien, die für die Ermittlung aktiver Lehrender berücksichtigt werden'),
                Config::get()->MASSMAIL_LECTURER_SEM_CATEGORIES,
                ['options' => $categories]
            )
        );

        $task = CronjobTask::findOneByClass(SendMassmailsJob::class);
        $job = CronjobSchedule::findOneByTask_id($task->id);

        $cron = new \Studip\Forms\Fieldset(_('Cronjob'));

        if (!$task->active || !$job->active) {
            $cron->addInput(
                new \Studip\Forms\InfoInput(
                    'inactive',
                    _('Achtung: Kein Versand'),
                    _('Der automatische Versand ist nicht aktiviert!')
                )
            );
        }

        $cron->addInput(
            new \Studip\Forms\NumberInput(
                'minutes',
                _('Abstand des Versands anstehender Nachrichten in Minuten'),
                abs($job->minute),
                ['min' => 1, 'max' => 59]
            )
        );
        $form->addPart($cron);

        $this->render_form($form);
    }

    /**
     * Stores the global massmail settings..
     * @return void
     * @throws AccessDeniedException
     */
    public function store_action()
    {
        CSRFProtection::verifyUnsafeRequest();

        Config::get()->store(
            'MASSMAIL_GC_DAYS',
            Request::int('cleanup', 7)
        );
        Config::get()->store(
            'MASSMAIL_LECTURER_SEM_CATEGORIES',
            Request::intArray('categories')
        );

        $task = CronjobTask::findOneByClass(SendMassmailsJob::class);
        $job = CronjobSchedule::findOneByTask_id($task->id);
        $job->minute = -1 * abs(Request::int('minutes'));
        $job->store();

        PageLayout::postSuccess('Die Einstellungen wurden gespeichert.');

        $this->relocate('massmail/settings');
    }

}
