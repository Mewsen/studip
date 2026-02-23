<?php
class Evaluation_ProfilesController extends AuthenticatedController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
        $current_user = User::findCurrent();
        if (!(($current_user->hasPermissionLevel('root') ||
            $current_user->hasRole('Zentraler Evaluationsadmin')) &&
            PluginManager::getInstance()->getPlugin(CoreEvaluation::class))) {
            throw new AccessDeniedException();
        }
    }

    public function index_action(): void
    {
        Navigation::activateItem('/evaluation/profiles');
        $this->profiles = QuestionnaireEvalCentralProfile::findBySQL(
            "INNER JOIN `semester_data`
            USING (`semester_id`)
            ORDER BY `semester_data`.`beginn` DESC"
        );
    }

    public function preedit_action()
    {
        $this->semesters = $this->getAvailableSemesters(true);
        $this->total_sem_count = Semester::countBySql();
        $this->render_template('evaluation/profiles/preedit');
    }

    public function edit_action(string $id = null): void
    {
        $sem_preselect = Request::get('sem_select');
        if ($id) {
            $profile = QuestionnaireEvalCentralProfile::find($id);
            $semesters = [$profile->semester_id => $profile->semester->name];
        } else {
            $profile_preselect = QuestionnaireEvalCentralProfile::find($sem_preselect);
            $profile = QuestionnaireEvalCentralProfile::build($profile_preselect);
            $semesters = $this->getAvailableSemesters();
        }
        $is_fill = $sem_preselect || $id;

        $template_array = [];
        Questionnaire::findEachBySQL(
            function($row) use (&$template_array) {
                $template_array[$row['questionnaire_id']] = $row['title'];
            },
            "`is_template` = 1"
        );

        $form = \Studip\Forms\Form::fromSORM($profile, [
            'legend' => $id ? htmlReady($profile->semester->name) . _(' bearbeiten') : _('Profil anlegen'),
            'fields' => [
                'semester_id' => [
                    'label'    => _('Semester'),
                    'required' => true,
                    'type'     => 'select',
                    'options'  => $semesters,
                    'value'    => $profile->semester_id ?? null
                ],
                'template_id' => [
                    'label'    => _('Vorlage'),
                    'required' => true,
                    'type'     => 'select',
                    'options'  => $template_array,
                    'value'    => $profile->template_id ?? null
                ],
                'optional_templates' => [
                    'label'   => _('Alternative Vorlagen'),
                    'type'    => 'multiselect',
                    'options' => $template_array,
                    'mapper'  => function($value) {
                        return implode(',', $value);
                    },
                    'value'   => $is_fill && $profile->optional_templates ?
                        explode(',', $profile->optional_templates) : null
                ],
                'startdate' => [
                    'label'    => _('Start'),
                    'name'     => 'startdate',
                    'required' => true,
                    'type'     => 'datetimepicker',
                    'value'    => $is_fill ? $profile->startdate : time() //TODO sem
                ],
                'stopdate' => [
                    'label'    => _('Ende'),
                    'required' => true,
                    'type'     => 'datetimepicker',
                    'value'    => $is_fill ? $profile->stopdate : time(), //TODO sem
                    'mindate'  => 'startdate'
                ],
                'anonymous' => [
                    'label' => _('Anonyme Teilnahme'),
                    'type'  => 'checkbox',
                    'value' => $is_fill ? $profile->anonymous : true
                ],
                'editanswers' => [
                    'label' => _('Antworten revidierbar'),
                    'type'  => 'checkbox',
                    'value' => $is_fill ? $profile->editanswers : false
                ],
                'resultvisibility' => [
                    'label'   => _('Zeitpunkt der Ergebnis-Einsicht'),
                    'type'    => 'select',
                    'options' => QuestionnaireEvalCentralProfile::getTranslatedVisibilityOptions(),
                    'value'   => $is_fill ? $profile->resultvisibility : 'never'
                ],
                'result_visible_for' => [
                    'label'   => _('Ergebnis-Einsicht für (Evaluations-Admins immer)'),
                    'type'    => 'select',
                    'options' => QuestionnaireEvalCentralProfile::getTranslatedVisibilityOptions(true),
                    'value'   => $is_fill ? $profile->result_visible_for : null
                ],
                'minimum_responses' => [
                    'label' => _('Mindestrücklauf'),
                    'type'  => 'number',
                    'value' => $is_fill ? $profile->minimum_responses : 8,
                    'min'   => 0
                ]
            ]
        ], $this->url_for('evaluation/profiles')
        )->setSuccessMessage(_('Erfolgreich gespeichert.'))->autoStore();

        PageLayout::setTitle(
            $id ? _('Profil bearbeiten: ') . htmlReady($profile->semester->name) : _('Neues Profil'));
        $this->render_form($form);
    }

    public function getAvailableSemesters(bool $reverse = false): array
    {
        $semesters = [];
        Semester::findEachBySQL(
            function($row) use (&$semesters) {
                $semesters[$row['semester_id']] = $row['name'];
            },
            $reverse ?
                "`semester_id` IN (SELECT `semester_id` FROM `questionnaire_eval_central_profiles`)
                ORDER BY `beginn` desc" :
                "`semester_id` NOT IN (SELECT `semester_id` FROM `questionnaire_eval_central_profiles`)
                    ORDER BY `beginn` desc"
        );
        return $semesters;
    }

    public function bulkdelete_action(): void
    {
        CSRFProtection::verifyUnsafeRequest();
        $profiles = QuestionnaireEvalCentralProfile::findMany(Request::optionArray('profiles'));
        foreach ($profiles as $profile) {
            $profile->delete();
        }
        $this->redirect('evaluation/profiles');
    }
}
