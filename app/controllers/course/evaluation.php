<?php
class Course_EvaluationController extends AuthenticatedController
{
    public function index_action()
    {
        Navigation::activateItem('course/evaluation');
        $this->render_template('course/evaluation/index', $this->layout);
    }
}
