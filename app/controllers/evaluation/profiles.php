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

    public function edit_action(): void
    {
        $profile = new QuestionnaireEvalCentralProfile();

        $template_array = [];
        Questionnaire::findEachBySQL(
          function($row) use (&$template_array) {
              $template_array[$row['questionnaire_id']] = $row['title'];
          },
          "`is_template` = 1"
        );

        $semesters = [];
        Semester::findEachBySQL(
            function($row) use (&$semesters) {
                $semesters[$row['semester_id']] = $row['name'];
            },
            "`semester_id` NOT IN (SELECT `semester_id` FROM `questionnaire_eval_central_profiles`)
                ORDER BY `beginn` desc"
        );

        $form = \Studip\Forms\Form::fromSORM($profile, [
            'legend' => 'Profil anlegen',
            'fields' => [
                'semester_id' => [
                    'label'    => _('Semester'),
                    'required' => true,
                    'type'     => 'select',
                    'options'  => $semesters
                ],
                'template_id' => [
                    'label'    => _('Vorlage'),
                    'required' => true,
                    'type'     => 'select',
                    'options'  => $template_array
                ],
                'optional_templates' => [
                    'label'   => _('Alternative Vorlagen'),
                    'type'    => 'multiselect',
                    'options' => $template_array,
                    'mapper'  => function($value) {
                        return implode(',', $value);
                    }
                ],
                'startdate' => [
                    'label'    => _('Start'),
                    'required' => true,
                    'type'     => 'datetimepicker',
                    'value'    => time() //TODO sem
                ],
                'stopdate' => [
                    'label'    => _('Ende'),
                    'required' => true,
                    'type'     => 'datetimepicker',
                    'value'    => time() //TODO sem
                ],
                'anonymous' => [
                    'label' => _('Anonyme Teilnahme'),
                    'type'  => 'checkbox',
                    'value' => true
                ],
                'editanswers' => [
                    'label' => _('Antworten revidierbar'),
                    'type'  => 'checkbox',
                    'value' => false
                ],
                'resultvisibility' => [
                    'label' => _('Zeitpunkt der Ergebnis-Einsicht'),
                    'type' => 'select',
                    'options' => QuestionnaireEvalCentralProfile::getTranslatedVisibilityOptions(),
                    'value' => 'never'
                ],
                'result_visible_for' => [
                    'label' => _('Ergebnis-Einsicht für (Evaluations-Admins immer)'),
                    'type' => 'select',
                    'options' => QuestionnaireEvalCentralProfile::getTranslatedVisibilityOptions(true)
                ],
                'minimum_responses' => [
                    'label' => _('Mindestrücklauf'),
                    'type' => 'number',
                    'value' => 8
                ]
            ]
        ], $this->url_for('evaluation/profiles')
        )->setSuccessMessage(_('Erfolgreich gespeichert.'))->autoStore();
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
