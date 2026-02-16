<?php
class Evaluation_ArchiveController extends AuthenticatedController
{
    public function index_action()
    {
        Navigation::activateItem('/evaluation/archive');

        $semesters = Semester::getAll();
        $list = new SelectWidget(
            _('Semester'),
            $this->url_for('evaluation/archive/set'),
            'sem_select'
        );
        foreach ($semesters as $semester) {
            $list->addElement(new SelectElement(
                $semester->id,
                htmlReady($semester->name),
                $semester->id === $_SESSION['evaluation_archive_sem']
            ));
        }
        Sidebar::Get()->addWidget($list);

        $this->evaluations = Questionnaire::findBySQL('1'); //TODO
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
        //TODO
    }
}
