<?php
class Evaluation_ProfilesController extends AuthenticatedController
{
    public function index_action(): void
    {
        Navigation::activateItem('/evaluation/profiles');
        $this->profiles = QuestionnaireEvalCentralProfile::findBySQL("1");
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
            "`semester_id` NOT IN (SELECT `semester_id` FROM `questionnaire_eval_central_profiles`)"
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
                    'label'   => _('Alternative Fragebögen'),
                    'type'    => 'multiselect',
                    'options' => $template_array
                ]
            ]
        ], $this->url_for('evaluation/profiles')
        )->setSuccessMessage(_('Erfolgreich gespeichert.'))->autoStore();
        $this->render_form($form);
    }
}
