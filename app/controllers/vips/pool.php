<?php
/**
 * vips/pool.php - assignment pool controller
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Elmar Ludwig
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 */

class Vips_PoolController extends AuthenticatedController
{
    /**
     * Callback function being called before an action is executed. If this
     * function does not return FALSE, the action will be called, otherwise
     * an error will be generated and processing will be aborted. If this function
     * already #rendered or #redirected, further processing of the action is
     * withheld.
     *
     * @param string  Name of the action to perform.
     * @param array   An array of arguments to the action.
     *
     * @return bool|void
     */
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        PageLayout::setHelpKeyword('Basis.Vips');
    }

    /**
     * Display all exercises that are available for this user.
     * Available in this case means the exercise is in a course where the user
     * is at least tutor.
     * Lecturer/tutor can select which exercise to edit/assign/delete.
     */
    public function exercises_action()
    {
        Navigation::activateItem('/contents/vips/exercises');
        PageLayout::setTitle(_('Meine Aufgaben'));

        Helpbar::get()->addPlainText('',
            _('Auf dieser Seite finden Sie eine Übersicht über alle Aufgaben, auf die Sie Zugriff haben.'));

        $range_type = $_SESSION['view_context'] ?? 'user';
        $range_type = Request::option('range_type', $range_type);
        $_SESSION['view_context'] = $range_type;

        $widget = new ViewsWidget();
        $widget->addLink(
            _('Persönliche Aufgabensammlung'),
            $this->url_for('vips/pool/exercises', ['range_type' => 'user'])
        )->setActive($range_type === 'user');
        $widget->addLink(
            _('Aufgaben in Veranstaltungen'),
            $this->url_for('vips/pool/exercises', ['range_type' => 'course'])
        )->setActive($range_type === 'course');
        Sidebar::get()->addWidget($widget);

        $sort = Request::option('sort', 'mkdate');
        $desc = Request::int('desc', $sort === 'mkdate');
        $page = Request::int('page', 1);
        $size = Config::get()->ENTRIES_PER_PAGE;

        $search_filter = Request::getArray('search_filter') + ['search_string' => '', 'exercise_type' => ''];
        $search_filter['search_string'] = Request::get('pool_search_parameter', $search_filter['search_string']);
        $search_filter['exercise_type'] = Request::get('exercise_type', $search_filter['exercise_type']);

        if (Request::submitted('start_search') || Request::int('pool_search')) {
            $search_filter = [
                'search_string' => Request::get('pool_search_parameter'),
                'exercise_type' => Request::get('exercise_type')
            ];
        } else if (empty($search_filter) || Request::submitted('reset_search')) {
            $search_filter = array_fill_keys(['search_string', 'exercise_type'], '');
        }

        // get exercises of this user and where he/she has permission
        if ($range_type === 'course') {
            $course_ids = array_column(VipsModule::getActiveCourses($GLOBALS['user']->id), 'id');
        } else {
            $course_ids = [$GLOBALS['user']->id];
        }

        // set up the sql query for the quicksearch
        $sql = "SELECT etask_tasks.id, etask_tasks.title FROM etask_tasks
                JOIN etask_test_tasks ON etask_tasks.id = etask_test_tasks.task_id
                JOIN etask_assignments USING (test_id)
                WHERE etask_assignments.range_id IN ('" . implode("','", $course_ids) . "')
                  AND etask_assignments.type IN ('exam', 'practice', 'selftest')
                  AND (etask_tasks.title LIKE :input OR etask_tasks.description LIKE :input)
                  AND IF(:exercise_type = '', 1, etask_tasks.type = :exercise_type)
                ORDER BY title";
        $search = new SQLSearch($sql, _('Titel der Aufgabe'));

        $widget = new VipsSearchWidget($this->url_for('vips/pool/exercises', ['exercise_type' => $search_filter['exercise_type']]));
        $widget->addNeedle(_('Suche'), 'pool_search', true, $search, 'function(id, name) { this.value = name; this.form.submit(); }', $search_filter['search_string']);
        Sidebar::get()->addWidget($widget);

        $widget = new SelectWidget(_('Aufgabentyp'), $this->url_for('vips/pool/exercises', ['pool_search_parameter' => $search_filter['search_string']]), 'exercise_type');
        $element = new SelectElement('', _('Alle Aufgabentypen'));
        $widget->addElement($element);
        Sidebar::get()->addWidget($widget);

        foreach (Exercise::getExerciseTypes() as $type => $entry) {
            $element = new SelectElement($type, $entry['name'], $type === $search_filter['exercise_type']);
            $widget->addElement($element);
        }

        $result = $this->getAllExercises($course_ids, $sort, $desc, $search_filter);

        $this->sort = $sort;
        $this->desc = $desc;
        $this->page = $page;
        $this->count = count($result);
        $this->exercises = array_slice($result, $size * ($page - 1), $size);
        $this->search_filter = $search_filter;
    }

    /**
     * Display all assignments that are available for this user.
     * Available in this case means the assignment is in a course where the user
     * is at least tutor.
     * Lecturer/tutor can select which assignment to edit/delete.
     */
    public function assignments_action()
    {
        Navigation::activateItem('/contents/vips/assignments');
        PageLayout::setTitle(_('Meine Aufgabenblätter'));

        Helpbar::get()->addPlainText('',
            _('Auf dieser Seite finden Sie eine Übersicht über alle Aufgabenblätter, auf die Sie Zugriff haben.'));

        $range_type = $_SESSION['view_context'] ?? 'user';
        $range_type = Request::option('range_type', $range_type);
        $_SESSION['view_context'] = $range_type;

        $widget = new ActionsWidget();
        $widget->addLink(
            _('Aufgabenblatt erstellen'),
            $this->url_for('vips/sheets/edit_assignment'),
            Icon::create('add')
        );
        $widget->addLink(
            _('Aufgabenblatt kopieren'),
            $this->url_for('vips/sheets/copy_assignment_dialog'),
            Icon::create('copy')
        )->asDialog('size=1200x800');
        $widget->addLink(
            _('Aufgabenblatt importieren'),
            $this->url_for('vips/sheets/import_assignment_dialog'),
            Icon::create('import')
        )->asDialog('size=auto');
        Sidebar::get()->addWidget($widget);

        $widget = new ViewsWidget();
        $widget->addLink(
            _('Persönliche Aufgabensammlung'),
            $this->url_for('vips/pool/assignments', ['range_type' => 'user'])
        )->setActive($range_type === 'user');
        $widget->addLink(
            _('Aufgaben in Veranstaltungen'),
            $this->url_for('vips/pool/assignments', ['range_type' => 'course'])
        )->setActive($range_type === 'course');
        Sidebar::get()->addWidget($widget);

        $sort = Request::option('sort', 'mkdate');
        $desc = Request::int('desc', $sort === 'mkdate');
        $page = Request::int('page', 1);
        $size = Config::get()->ENTRIES_PER_PAGE;

        $search_filter = Request::getArray('search_filter') + ['search_string' => '', 'assignment_type' => ''];
        $search_filter['search_string'] = Request::get('pool_search_parameter', $search_filter['search_string']);
        $search_filter['assignment_type'] = Request::get('assignment_type', $search_filter['assignment_type']);

        // get assignments of this user and where he/she has permission
        if ($range_type === 'course') {
            $course_ids = array_column(VipsModule::getActiveCourses($GLOBALS['user']->id), 'id');
        } else {
            $course_ids = [$GLOBALS['user']->id];
        }

        // set up the sql query for the quicksearch
        $sql = "SELECT etask_assignments.id, etask_tests.title FROM etask_tests
                JOIN etask_assignments ON etask_tests.id = etask_assignments.test_id
                WHERE etask_assignments.range_id IN ('" . implode("','", $course_ids) . "')
                  AND etask_assignments.type IN ('exam', 'practice', 'selftest')
                  AND (etask_tests.title LIKE :input OR etask_tests.description LIKE :input)
                  AND IF(:assignment_type = '', 1, etask_assignments.type = :assignment_type)
                ORDER BY title";
        $search = new SQLSearch($sql, _('Titel des Aufgabenblatts'));

        $widget = new VipsSearchWidget($this->url_for('vips/pool/assignments', ['assignment_type' => $search_filter['assignment_type']]));
        $widget->addNeedle(_('Suche'), 'pool_search', true, $search, 'function(id, name) { this.value = name; this.form.submit(); }', $search_filter['search_string']);
        Sidebar::get()->addWidget($widget);

        $widget = new SelectWidget(_('Modus'), $this->url_for('vips/pool/assignments', ['pool_search_parameter' => $search_filter['search_string']]), 'assignment_type');
        $element = new SelectElement('', _('Beliebiger Modus'));
        $widget->addElement($element);
        Sidebar::get()->addWidget($widget);

        foreach (VipsAssignment::getAssignmentTypes() as $type => $entry) {
            $element = new SelectElement($type, $entry['name'], $type === $search_filter['assignment_type']);
            $widget->addElement($element);
        }

        $result = $this->getAllAssignments($course_ids, $sort, $desc, $search_filter);

        $this->sort = $sort;
        $this->desc = $desc;
        $this->page = $page;
        $this->count = count($result);
        $this->assignments = array_slice($result, $size * ($page - 1), $size);
        $this->search_filter = $search_filter;
    }

    /**
     * Get all matching exercises from a list of courses in given order.
     * If $search_filter is not empty, search filters are applied.
     *
     * @param course_ids    list of courses to get exercises from
     * @param sort          sort exercise list by this property
     * @param desc          true if sort direction is descending
     * @param search_filter the currently active search filter
     *
     * @return array with data of all matching exercises
     */
    public function getAllExercises($course_ids, $sort, $desc, $search_filter)
    {
        $db = DBManager::get();

        // check if some filters are active
        $search_string = $search_filter['search_string'];
        $exercise_type = $search_filter['exercise_type'];

        $sql = "SELECT etask_tasks.*,
                       auth_user_md5.Nachname,
                       auth_user_md5.Vorname,
                       etask_assignments.id AS assignment_id,
                       etask_assignments.range_id,
                       etask_assignments.range_type,
                       etask_tests.title AS test_title
                FROM etask_tasks
                LEFT JOIN auth_user_md5 USING(user_id)
                JOIN etask_test_tasks ON etask_tasks.id = etask_test_tasks.task_id
                JOIN etask_tests ON etask_tests.id = etask_test_tasks.test_id
                JOIN etask_assignments USING (test_id)
                WHERE etask_assignments.range_id IN (:course_ids)
                  AND etask_assignments.type IN ('exam', 'practice', 'selftest') " .
                ($search_string ? 'AND (etask_tasks.title LIKE :input OR
                                        etask_tasks.description LIKE :input) ' : '') .
                ($exercise_type ? 'AND etask_tasks.type = :exercise_type ' : '') .
               "ORDER BY :sort :desc, title";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':course_ids', $course_ids);
        $stmt->bindValue(':input', '%' . $search_string . '%');
        $stmt->bindValue(':exercise_type', $exercise_type);
        $stmt->bindValue(':sort', $sort, StudipPDO::PARAM_COLUMN);
        $stmt->bindValue(':desc', $desc ? 'DESC' : 'ASC', StudipPDO::PARAM_COLUMN);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all matching assignments from a list of courses in given order.
     * If $search_filter is not empty, search filters are applied.
     *
     * @param course_ids    list of courses to get assignments from
     * @param sort          sort assignment list by this property
     * @param desc          true if sort direction is descending
     * @param search_filter the currently active search filter
     *
     * @return array with data of all matching assignments
     */
    public function getAllAssignments($course_ids, $sort, $desc, $search_filter)
    {
        $db = DBManager::get();

        // check if some filters are active
        $search_string = $search_filter['search_string'];
        $assignment_type = $search_filter['assignment_type'];
        $types = $assignment_type ? [$assignment_type] : ['exam', 'practice', 'selftest'];

        $sql = "SELECT etask_assignments.*,
                       etask_tests.title AS test_title,
                       auth_user_md5.Nachname,
                       auth_user_md5.Vorname,
                       seminare.Name,
                       (SELECT MIN(beginn) FROM semester_data
                        JOIN semester_courses USING(semester_id)
                        WHERE course_id = Seminar_id) AS start_time
                FROM etask_tests
                LEFT JOIN auth_user_md5 USING(user_id)
                JOIN etask_assignments ON etask_tests.id = etask_assignments.test_id
                LEFT JOIN seminare ON etask_assignments.range_id = seminare.Seminar_id
                WHERE etask_assignments.range_id IN (:course_ids)
                  AND etask_assignments.type IN (:types) " .
                ($search_string ? 'AND (etask_tests.title LIKE :input OR
                                        etask_tests.description LIKE :input) ' : '') .
               "ORDER BY :sort :desc, title";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':course_ids', $course_ids);
        $stmt->bindValue(':input', '%' . $search_string . '%');
        $stmt->bindValue(':types', $types);
        $stmt->bindValue(':sort', $sort, StudipPDO::PARAM_COLUMN);
        $stmt->bindValue(':desc', $desc ? 'DESC' : 'ASC', StudipPDO::PARAM_COLUMN);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete a list of exercises from their respective assignments.
     */
    public function delete_exercises_action()
    {
        CSRFProtection::verifyUnsafeRequest();

        $exercise_ids = Request::intArray('exercise_ids');
        $deleted = 0;

        foreach ($exercise_ids as $exercise_id => $assignment_id) {
            $assignment = VipsAssignment::find($assignment_id);
            VipsModule::requireEditPermission($assignment, $exercise_id);

            if (!$assignment->isLocked()) {
                $assignment->test->removeExercise($exercise_id);
                ++$deleted;
            }
        }

        if ($deleted > 0) {
            PageLayout::postSuccess(sprintf(ngettext('Die Aufgabe wurde gelöscht.', 'Es wurden %s Aufgaben gelöscht.', $deleted), $deleted));
        }

        if ($deleted < count($exercise_ids)) {
            PageLayout::postError(_('Einige Aufgaben konnten nicht gelöscht werden, da die Aufgabenblätter gesperrt sind.'), [
                _('Falls Sie diese wirklich löschen möchten, müssen Sie zuerst die Lösungen aller Teilnehmenden zurücksetzen.')
            ]);
        }

        $this->redirect('vips/pool/exercises');
    }

    /**
     * Dialog for copying a list of exercises into an existing assignment.
     */
    public function copy_exercises_dialog_action()
    {
        PageLayout::setTitle(_('Aufgaben in vorhandenes Aufgabenblatt kopieren'));

        $this->exercise_ids = Request::intArray('exercise_ids');
        $this->courses = VipsModule::getActiveCourses($GLOBALS['user']->id);
    }

    /**
     * Copy the selected exercises into the selected assignment.
     */
    public function copy_exercises_action()
    {
        CSRFProtection::verifyUnsafeRequest();

        $exercise_ids = Request::intArray('exercise_ids');
        $target_assignment_id = Request::int('assignment_id');
        $target_assignment = VipsAssignment::find($target_assignment_id);

        VipsModule::requireEditPermission($target_assignment);

        if (!$target_assignment->isLocked()) {
            foreach ($exercise_ids as $exercise_id => $assignment_id) {
                $assignment = VipsAssignment::find($assignment_id);
                VipsModule::requireEditPermission($assignment);

                $exercise_ref = VipsExerciseRef::find([$assignment->test_id, $exercise_id]);
                $exercise_ref->copyIntoTest($target_assignment->test_id);
            }

            PageLayout::postSuccess(ngettext('Die Aufgabe wurde kopiert.', 'Die Aufgaben wurden kopiert.', count($exercise_ids)));
        }

        $this->redirect('vips/pool/exercises');
    }

    /**
     * Dialog for moving a list of exercises into an existing assignment.
     */
    public function move_exercises_dialog_action()
    {
        PageLayout::setTitle(_('Aufgaben in vorhandenes Aufgabenblatt verschieben'));

        $this->exercise_ids = Request::intArray('exercise_ids');
        $this->courses = VipsModule::getActiveCourses($GLOBALS['user']->id);
    }

    /**
     * Move the selected exercises into the selected assignment.
     */
    public function move_exercises_action()
    {
        CSRFProtection::verifyUnsafeRequest();

        $exercise_ids = Request::intArray('exercise_ids');
        $target_assignment_id = Request::int('assignment_id');
        $target_assignment = VipsAssignment::find($target_assignment_id);
        $moved = 0;

        VipsModule::requireEditPermission($target_assignment);

        if (!$target_assignment->isLocked()) {
            foreach ($exercise_ids as $exercise_id => $assignment_id) {
                $assignment = VipsAssignment::find($assignment_id);
                VipsModule::requireEditPermission($assignment);

                if (!$assignment->isLocked()) {
                    $exercise_ref = VipsExerciseRef::find([$assignment->test_id, $exercise_id]);
                    $exercise_ref->moveIntoTest($target_assignment->test_id);
                    ++$moved;
                }
            }
        }

        if ($moved > 0) {
            PageLayout::postSuccess(sprintf(ngettext('Die Aufgabe wurde verschoben.', 'Es wurden %s Aufgaben verschoben.', $moved), $moved));
        }

        if ($moved < count($exercise_ids)) {
            PageLayout::postError(_('Einige Aufgaben konnten nicht verschoben werden, da die Aufgabenblätter gesperrt sind.'));
        }

        $this->redirect('vips/pool/exercises');
    }

    /**
     * Return the appropriate CSS class for sortable column (if any).
     *
     * @param boolean $sort sort by this column
     * @param boolean $desc set sort direction
     */
    public function sort_class(bool $sort, ?bool $desc): string
    {
        return $sort ? ($desc ? 'sortdesc' : 'sortasc') : '';
    }

    /**
     * Render a generic page chooser selector. The first occurence of '%d'
     * in the URL is replaced with the selected page number.
     *
     * @param string      $url       URL for one of the pages
     * @param string      $count     total number of entries
     * @param string      $page      current page to display
     * @param string|null $dialog    Optional dialog attribute content
     * @param int|null    $page_size page size (defaults to system default)
     * @return mixed
     */
    function page_chooser(string $url, string $count, string $page, ?string $dialog = null, ?int $page_size = null)
    {
        $template = $GLOBALS['template_factory']->open('shared/pagechooser');
        $template->dialog = $dialog;
        $template->num_postings = $count;
        $template->page = $page;
        $template->perPage = $page_size ?: Config::get()->ENTRIES_PER_PAGE;
        $template->pagelink = str_replace('%%25d', '%d', str_replace('%', '%%', $url));

        return $template->render();
    }
}
