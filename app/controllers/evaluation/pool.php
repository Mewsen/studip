<?php
class Evaluation_PoolController extends AuthenticatedController
{
    public function index_action()
    {
        Navigation::activateItem('/evaluation/pool');
        $this->templates = Questionnaire::findBySQL("`is_template` = 1");
    }
}
