<?php
class Evaluation_ArchiveController extends AuthenticatedController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
        $current_user = User::findCurrent();
        if (!(($current_user->hasPermissionLevel('root') ||
                $current_user->hasRole('Zentraler Evaluationsadmin')) &&
            PluginManager::getInstance()->getPlugin(CoreEvaluation::class))) {
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
        $selected_sem = $_SESSION['evaluation_archive_sem'] ?? Semester::findCurrent()->id;
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
        $selected_inst = $_SESSION['evaluation_archive_inst'] ?? $institutes[0]['Institut_id'];
        foreach ($institutes as $institute) {
            $inst_list->addElement(
                new SelectElement(
                    $institute['Institut_id'],
                    htmlReady($institute['Name']),
                    $institute['Institut_id'] === $selected_inst
            ));
        }
        Sidebar::Get()->addWidget($inst_list);

        $this->eval_assignments = QuestionnaireEvalAssignment::findBySQL(
            "`questionnaire_id` IS NOT NULL AND `applied` = 1
            AND `semester_id` = ? AND `institute_id` = ?",
            [$selected_sem, $selected_inst]);
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
                $assignments = Request::optionArray('assignments');
                foreach ($assignments as $assignment_id) {
                    $assignment = QuestionnaireEvalAssignment::find($assignment_id);
                    if ($assignment->delete()) {
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
