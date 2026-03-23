<?php
class Evaluation_ArchiveController extends AuthenticatedController
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
        Navigation::activateItem('/evaluation/archive');

        $semesters = array_reverse(Semester::getAll());
        $sem_list = new SelectWidget(
            _('Semester'),
            $this->url_for('evaluation/archive/set'),
            'sem_select'
        );
        $selected_sem = $_SESSION['evaluation_archive_sem'] ?? 'all';
        $sem_list->addElement(new SelectElement(
           'all',
           _('Alle'),
            'all' === $selected_sem
        ));
        foreach ($semesters as $semester) {
            $sem_list->addElement(new SelectElement(
                $semester->id,
                htmlReady($semester->name),
                $semester->id === $selected_sem
            ));
        }
        Sidebar::Get()->addWidget($sem_list);

        $institutes = Institute::getInstitutes();
        $inst_list = new SelectWidget(
            _('Einrichtungen'),
            $this->url_for('evaluation/archive/set'),
            'inst_select'
        );
        $selected_inst = $_SESSION['evaluation_archive_inst'] ?? 'all';
        $inst_list->addElement(new SelectElement(
            'all',
           _('Alle'),
            'all' === $selected_inst
        ));
        foreach ($institutes as $institute) {
            $inst_list->addElement(
                new SelectElement(
                    $institute['Institut_id'],
                    htmlReady($institute['Name']),
                    $institute['Institut_id'] === $selected_inst
            ));
        }
        Sidebar::Get()->addWidget($inst_list);

        $query = SQLQuery::table('questionnaires')
            ->join('questionnaire_eval_assignments', 'questionnaire_eval_assignments',
                '`questionnaire_eval_assignments`.`questionnaire_id` = `questionnaires`.`questionnaire_id`')
            ->where('`applied` = 1');
        if ($selected_sem !== 'all') {
            $query->where('semester_id', '`semester_id` = ?', [$selected_sem]);
        }
        if ($selected_inst !== 'all') {
            $query->where('institute_id', '`institute_id` = ?', [$selected_inst]);
        }
        $query->orderBy('`questionnaires`.`startdate` DESC');
        $this->evaluations = $query->fetchAll(Questionnaire::class);
    }

    public function set_action()
    {
        if (Request::get('sem_select')) {
            $_SESSION['evaluation_archive_sem'] = Request::get('sem_select');
        }
        if (Request::get('inst_select')) {
            $_SESSION['evaluation_archive_inst'] = Request::get('inst_select');
        }
        $this->redirect('evaluation/archive');
    }

    public function bulk_action($action)
    {
        CSRFProtection::verifyUnsafeRequest();
        switch ($action) {
            case 'delete':
                $evaluations = Request::optionArray('evaluations');
                foreach ($evaluations as $evaluation_id) {
                    $evaluation = Questionnaire::find($evaluation_id);
                    if ($evaluation->delete()) {
                        PageLayout::postSuccess(_('Evaluation erfolgreich gelöscht.'));
                    } else {
                        PageLayout::postError(_('Es ist ein Fehler aufgetreten.'));
                    }
                }
                break;
            case 'export':
                //TODO
                break;
        }
        $this->redirect('evaluation/archive');
    }
}
