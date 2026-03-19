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
        $form->addInput(new \Studip\Forms\HiddenInput(
            'eval_courses',
            _('Veranstaltungen'),
            implode(',', $courseIds)
        ));
        $form->addInput(new \Studip\Forms\HiddenInput(
            'semester_id',
            _('Semester'),
            $semester_id
        ));
        $part = new \Studip\Forms\Fieldset(_('Standardwerte aus zentralem Profil anpassen'));
        $part->addInput(
            new \Studip\Forms\SelectInput(
                'template_id',
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
        $course_ids = explode(',', Request::get('eval_courses'));
        $template = Questionnaire::find(Request::get('template_id'));
        $profile = QuestionnaireEvalCentralProfile::find(Request::get('semester_id'));

        foreach ($course_ids as $course_id) {
            $course = Course::find($course_id);
            if (!$course) continue;

            $questionnaire = $this->createEvaluation($template, $profile);
            $this->assignEvaluation($course, $template->getId(), $questionnaire->getId());
        }

        //TODO success message
        $this->relocate('admin/courses');
    }

    public function createEvaluation(Questionnaire $template,
                                     QuestionnaireEvalCentralProfile $profile): Questionnaire
    {
        $questionnaire = new Questionnaire();
        $questionnaire->template_id = $template->getId();
        $questionnaire->title = sprintf('%s [%s]', $template->title, _('Evaluation'));
        $questionnaire->user_id = User::findCurrent()->getId();
        $questionnaire->startdate = Request::get('startdate');
        $questionnaire->stopdate = Request::get('stopdate');
        $questionnaire->anonymous = $profile->anonymous;
        $questionnaire->resultvisibility = $profile->resultvisibility;
        $questionnaire->result_visible_for = $profile->result_visible_for;
        $questionnaire->minimum_responses = $profile->minimum_responses;
        $questionnaire->editanswers = $profile->editanswers;
        $questionnaire->mkdate = time();
        $questionnaire->chdate = time();
        $questionnaire->store();

        foreach ($template->questions as $question) {
            $new_question = QuestionnaireQuestion::build($question->toArray());
            $new_question->setId($new_question->getNewId());
            $new_question->questionnaire_id = $questionnaire->getId();
            $new_question->questiondata = $question->questiondata;
            $new_question->mkdate = time();
            if (isset($question->template_question_id)) {
                $new_question->template_question_id = $question->template_question_id;
            } else {
                $new_question->template_question_id = $question->question_id;
            }
            $new_question->store();
        }

        return $questionnaire;
    }

    public function assignEvaluation(Course $course, string $template_id, string $questionnaire_id)
    {
        $eval = new QuestionnaireEvalAssignment();

        $persons = [];
        $query = "SELECT `Nachname`, `Vorname` FROM `seminar_user`
                  INNER JOIN `auth_user_md5` ON `auth_user_md5`.`user_id` = `seminar_user`.`user_id`
                  WHERE `seminar_id` = ?
                    AND `seminar_user`.`status` IN (?)
                  ORDER BY `status` DESC, `position`, `Nachname`, `Vorname`";
        DBManager::get()->fetchAll($query, [$course->getId(), ['tutor', 'dozent']],
            function ($row) use (&$persons) {
                $persons[] = $row['Vorname'] . ' ' . $row['Nachname'];
            }
        );

        $eval->course_id = $course->getId();
        $eval->template_id = $template_id;
        $eval->questionnaire_id = $questionnaire_id;
        $eval->semester_id = $GLOBALS['user']->cfg->MY_COURSES_SELECTED_CYCLE;
        $eval->applied = 1;
        $eval->course_metadata = json_encode([
            'course_title' => $course->name,
            'evaluated_persons' => $persons
        ]);
        $eval->institute_id = $course->institut_id;

        $eval->store();
    }
}
