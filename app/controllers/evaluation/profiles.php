<?php
class Evaluation_ProfilesController extends AuthenticatedController
{
    public function index_action(): void
    {
        Navigation::activateItem('/evaluation/profiles');
        $this->profiles = QuestionnaireEvalCentralProfile::findBySQL(
            "INNER JOIN `semester_data`
            USING (`semester_id`)
            ORDER BY `semester_data`.`beginn` DESC"
        );
    }

    public function edit_action(string $id = null): void
    {
        if ($id) {
            $profile = QuestionnaireEvalCentralProfile::find($id);
            $semesters = [$profile->semester_id => $profile->semester->name];
        } else {
            $profile = new QuestionnaireEvalCentralProfile();
            $semesters = [];
            Semester::findEachBySQL(
                function($row) use (&$semesters) {
                    $semesters[$row['semester_id']] = $row['name'];
                },
                "`semester_id` NOT IN (SELECT `semester_id` FROM `questionnaire_eval_central_profiles`)
                ORDER BY `beginn` desc"
            );
        }

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
                    'value'   => $id && $profile->optional_templates ?
                        explode(',', $profile->optional_templates) : null
                ],
                'startdate' => [
                    'label'    => _('Start'),
                    'name'     => 'startdate',
                    'required' => true,
                    'type'     => 'datetimepicker',
                    'value'    => $id ? $profile->startdate : time() //TODO sem
                ],
                'stopdate' => [
                    'label'    => _('Ende'),
                    'required' => true,
                    'type'     => 'datetimepicker',
                    'value'    => $id ? $profile->stopdate : time(), //TODO sem
                    'mindate'  => 'startdate'
                ],
                'anonymous' => [
                    'label' => _('Anonyme Teilnahme'),
                    'type'  => 'checkbox',
                    'value' => $id ? $profile->anonymous : true
                ],
                'editanswers' => [
                    'label' => _('Antworten revidierbar'),
                    'type'  => 'checkbox',
                    'value' => $id ? $profile->editanswers : false
                ],
                'resultvisibility' => [
                    'label'   => _('Zeitpunkt der Ergebnis-Einsicht'),
                    'type'    => 'select',
                    'options' => QuestionnaireEvalCentralProfile::getTranslatedVisibilityOptions(),
                    'value'   => $id ? $profile->resultvisibility : 'never'
                ],
                'result_visible_for' => [
                    'label'   => _('Ergebnis-Einsicht für (Evaluations-Admins immer)'),
                    'type'    => 'select',
                    'options' => QuestionnaireEvalCentralProfile::getTranslatedVisibilityOptions(true),
                    'value'   => $id ? $profile->result_visible_for : null
                ],
                'minimum_responses' => [
                    'label' => _('Mindestrücklauf'),
                    'type'  => 'number',
                    'value' => $id ? $profile->minimum_responses : 8,
                    'min'   => 0
                ]
            ]
        ], $this->url_for('evaluation/profiles')
        )->setSuccessMessage(_('Erfolgreich gespeichert.'))->autoStore();

        PageLayout::setTitle(
            $id ? _('Profil bearbeiten: ') . htmlReady($profile->semester->name) : _('Neues Profil'));
        $this->render_form($form);
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
