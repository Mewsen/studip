<?php
class Evaluation_PoolController extends AuthenticatedController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
        if (!EvaluationHelper::isPermittedEvaluationAccess()) {
            throw new AccessDeniedException();
        }
    }

    public function index_action()
    {
        Navigation::activateItem('/evaluation/pool');
        $this->templates = Questionnaire::findBySQL("`is_template` = 1");
    }

    public function template_enable_action(Questionnaire $template)
    {
        $template->template_is_enabled = (int)!$template->template_is_enabled;
        $template->store();
        $this->redirect('evaluation/pool');
    }
}
