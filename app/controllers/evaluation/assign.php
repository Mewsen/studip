<?php

class Evaluation_AssignController extends AuthenticatedController
{
    public function before_filter(&$action, &$args)
    {
        if (!EvaluationHelper::isPermittedEvaluationAccess()) {
            throw new AccessDeniedException();
        }
        parent::before_filter($action, $args);
    }

    public function index_action()
    {
        $courseIds = Request::optionArray('evaluation_courses');
        $courses = Course::findMany($courseIds);

        $semester_id = $GLOBALS['user']->cfg->MY_COURSES_SELECTED_CYCLE;
        $profile = QuestionnaireEvalCentralProfile::find($semester_id);
        if (count($courses) === 0 || !$semester_id || !$profile) {
            PageLayout::postWarning(
                _('Es wurde keine Veranstaltung gewählt, oder es existiert kein Profil zu diesem Semester.'));
            $this->relocate('admin/courses');
            return;
        }

        $templates = [];
        $templates[$profile->template_id] = $profile->template->title;
        Questionnaire::findEachBySQL(
            function ($row) use (&$templates) {
                $templates[$row['questionnaire_id']] = $row['title'];
            },
            '`questionnaire_id` IN (?)',
            [explode(',', $profile->optional_templates)]
        );
        $form = \Studip\Forms\Form::create();
        $form->setURL($this->url_for('evaluation/assign/save'));
        $part = new \Studip\Forms\Fieldset(_('Standardwerte aus zentralem Profil anpassen'));
        $part->addInput(
            new \Studip\Forms\SelectInput(
                'template',
                _('Vorlage'),
                $profile->template_id,
                ['options' => $templates]
            )
        );
        $part->addInput(
            new \Studip\Forms\DatetimepickerInput(
                'startdate',
                _('Start'),
                $profile->startdate,
                [
                    'mindate' => $profile->semester->beginn,
                    'maxdate' => $profile->semester->ende
                ]
            )
        );
        $part->addInput(
            new \Studip\Forms\DatetimepickerInput(
                'stopdate',
                _('Ende'),
                $profile->stopdate,
                [
                    'mindate' => 'startdate',
                    'maxdate' => $profile->semester->ende
                ]
            )
        );
        $form->addPart($part);
        $this->render_form($form);
    }

    public function save_action()
    {
        //TODO
        $this->relocate('admin/courses');
    }
}
