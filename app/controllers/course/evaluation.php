<?php
class Course_EvaluationController extends AuthenticatedController
{
    public function index_action()
    {
        Navigation::activateItem('course/evaluation');
        $this->evaluations = Questionnaire::findBySQL(
            "INNER JOIN `questionnaire_eval_assignments` USING(`questionnaire_id`)
            WHERE `course_id` = ? AND `applied` = 1
            ORDER BY `questionnaire_eval_assignments`.`startdate` DESC",
            [Context::getId()]);
        $this->render_template('course/evaluation/index', $this->layout);
    }
}
