<?php
class Evaluation_ArchiveController extends AuthenticatedController
{
    public function index_action()
    {
        Navigation::activateItem('/evaluation/archive');

        $semesters = array_reverse(Semester::getAll());
        $list = new SelectWidget(
            _('Semester'),
            $this->url_for('evaluation/archive/set'),
            'sem_select'
        );
        $selected_sem = $_SESSION['evaluation_archive_sem'] ?? Semester::findCurrent()->id;
        foreach ($semesters as $semester) {
            $list->addElement(new SelectElement(
                $semester->id,
                htmlReady($semester->name),
                $semester->id === $selected_sem
            ));
        }
        Sidebar::Get()->addWidget($list);

        $this->eval_assignments = QuestionnaireEvalAssignment::findBySQL(
            "`semester_id` = ?",
            [$selected_sem]);
    }

    public function set_action()
    {
        if (Request::get('sem_select')) {
            $_SESSION['evaluation_archive_sem'] = Request::get('sem_select');
        }
        $this->redirect('evaluation/archive');
    }

    public function bulk_action($action)
    {
        CSRFProtection::verifyUnsafeRequest();
        //TODO (both eval & assign)
    }
}
