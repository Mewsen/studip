<?php
/**
 * restricted_courses.php - administration of admission restrictions
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      André Noack <noack@data-quest.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 * @package     admin
 */

class Admission_RestrictedCoursesController extends AuthenticatedController
{
    /**
     * common tasks for all actions
     */
    function before_filter (&$action, &$args)
    {
        parent::before_filter($action, $args);
        PageLayout::setTitle(_('Teilnahmebeschränkte Veranstaltungen'));
        Navigation::activateItem('/browse/coursesets/restricted_courses');
    }

    /**
     * Shows a list of courses with restricted participant numbers.
     */
    function index_action()
    {

        $actions = new ActionsWidget();
        $actions->addLink(_("Export"), $this->url_for('admission/restricted_courses', ['csv' => 1]), Icon::create('export'));
        Sidebar::get()->addWidget($actions);

        $sem_condition = "";
        foreach (words('current_institut_id sem_name_prefix') as $param) {
            $this->$param = $_SESSION[get_class($this)][$param] ?? null;
        }
        if (Request::isPost()) {
            if (Request::submitted('choose_institut')) {
                $this->current_institut_id = Request::option('choose_institut_id');
                $this->current_semester_id = Request::option('select_semester_id');
                $this->sem_name_prefix = trim(Request::get('sem_name_prefix'));
            }
        }
        if (!$this->current_institut_id) {
            $this->current_institut_id = 'all';
        }
        if (!$this->current_semester_id) {
            $this->current_semester_id = $_SESSION['_default_sem'];
        } else {
            $_SESSION['_default_sem'] = $this->current_semester_id;
        }
        $semester = Semester::find($this->current_semester_id);
        $sem_condition .= "
            AND (`semester_courses`.`semester_id` IS NULL OR `semester_courses`.`semester_id` = " . DBManager::get()->quote($semester->getId()) . ")
        ";
        if ($this->sem_name_prefix) {
            $sem_condition .= sprintf('AND (`seminare`.`Name` LIKE %1$s OR `seminare`.`VeranstaltungsNummer` LIKE %1$s) ', DBManager::get()->quote($this->sem_name_prefix . '%'));
        }
        if ($GLOBALS['perm']->have_perm('dozent')) {
            $this->my_inst = $this->get_institutes($sem_condition);
        }
        $this->courses = $this->get_courses($sem_condition);

        if (!isset($_SESSION[get_class($this)])) {
            $_SESSION[get_class($this)] = [];
        }
        foreach (words('current_institut_id sem_name_prefix') as $param) {
            $_SESSION[get_class($this)][$param] = $this->$param;
        }
        $this->additional_data = [];
        if (Request::get('csv')) {
            $captions = [_("Anmeldeset"),
                    _("Nummer"),
                    _("Name"),
                    _("max. Teilnehmende"),
                    _("Teilnehmende aktuell"),
                    _("Anzahl Anmeldungen"),
                    _("Anzahl vorl. Anmeldungen"),
                    _("Anzahl Warteliste"),
                    _("Platzverteilung"),
                    _("Startzeitpunkt"),
                    _("Endzeitpunkt")];
            $data = [];
            foreach ($this->courses as $course) {
                $additional_data = $this->getAdditionalCourseData($course);

                $start_time = '';

                $start_semester = $course->getStartSemester();
                if ($start_semester) {
                    $start_time = date('d.m.Y H:i', $start_semester->beginn);
                }

                $end_time = '';

                $end_semester = $course->getEndSemester();
                if ($end_semester) {
                    $end_time = date('d.m.Y H:i', $end_semester->ende);
                }

                $row = [];
                $row[] = $additional_data['courseset_name'];
                $row[] = $course->veranstaltungsnummer;
                $row[] = $course->name;
                $row[] = (int)$course->admission_turnout ?: '';
                $row[] = $additional_data['participant_count'] + $additional_data['accepted_count'];
                $row[] = (int)$additional_data['claiming_count'];
                $row[] = (int)$additional_data['accepted_count'];
                $row[] = (int)$additional_data['awaiting_count'];
                $row[] = $additional_data['distribution_time'] ? date('d.m.Y H:i', $additional_data['distribution_time']) : '';
                $row[] = $start_time;
                $row[] = $end_time;
                $data[] = $row;
            }

            $tmpname = md5(uniqid('tmp'));
            if (array_to_csv($data, $GLOBALS['TMP_PATH'].'/'.$tmpname, $captions)) {
                $this->redirect(
                    FileManager::getDownloadURLForTemporaryFile(
                        $tmpname,
                        'teilnahmebeschraenkteVeranstaltungen.csv'
                    )
                );
                return;
            }
        } else {
            //We need to loop over each course and fetch additional data to fill the
            //not_distributed_coursesets attribute before showing the view.
            foreach ($this->courses as $course) {
                $this->additional_data[$course->id] = $this->getAdditionalCourseData($course);
            }
        }

        if (is_array($this->not_distributed_coursesets)) {
            PageLayout::postInfo(
                _("Es existieren Anmeldesets, die zum Zeitpunkt der Platzverteilung nicht gelost wurden. Stellen Sie sicher, dass der Cronjob \"Losverfahren überprüfen\" ausgeführt wird."),
                array_unique($this->not_distributed_coursesets));
        }
    }

    /**
     * Fetches additional data for a course and sets the not_distributed_coursesets
     * attribute in some cases.
     *
     * @param Course $course The course to fetch data for.
     *
     * @return array An associative array with additional data.
     */
    protected function getAdditionalCourseData(Course $course) : array
    {
        $data = [];

        $courseset = $course->getCourseSet();
        if ($courseset) {
            $data['courseset_id']   = $courseset->getId();
            $data['courseset_name'] = $courseset->getName();
            if ($courseset->hasAlgorithmRun()) {
                $data['claiming_count'] = 0;
            } else {
                $data['claiming_count'] = count(AdmissionPriority::getPrioritiesByCourse($courseset->getId(), $course->id));
            }
            $data['distribution_time']  = $courseset->getSeatDistributionTime();
            if (
                $data['distribution_time'] < time() - 1000
                && !$courseset->hasAlgorithmRun()
            ) {
                $this->not_distributed_coursesets[] = $courseset->getName();
            }

            $timed_admission = $courseset->getAdmissionRule(TimedAdmission::class);
            if ($timed_admission) {
                $data['admission_start_time'] = $timed_admission->getStartTime();
                $data['admission_end_time']   = $timed_admission->getEndTime();
            }
        } else {
            $data['courseset_id']         = '';
            $data['courseset_name']       = '';
            $data['claiming_count']       = 0;
            $data['distribution_time']    = 0;
            $data['admission_start_time'] = '';
            $data['admission_end_time']   = '';
        }
        $data['participant_count'] = CourseMember::countByCourseAndStatus($course->id, ['user', 'autor']);
        $data['accepted_count']    = AdmissionApplication::countBySql(
            "`seminar_id` = :course_id AND `status` = 'accepted'",
            ['course_id' => $course->id]
        );
        $data['awaiting_count']    = AdmissionApplication::countBySql(
            "`seminar_id` = :course_id AND `status` = 'awaiting'",
            ['course_id' => $course->id]
        );

        return $data;
    }

    protected function get_courses($seminare_condition)
    {
        $chunks = explode('_', $this->current_institut_id);
        $institut_id = $chunks[0];
        $all = $chunks[1] ?? null;

        $parameters = [];

        $sql = "JOIN `seminar_courseset`
                    USING (`seminar_id`)
                JOIN `courseset_rule` csr
                    ON csr.`set_id` = `seminar_courseset`.`set_id`
                    AND csr.`type` = 'ParticipantRestrictedAdmission'
                LEFT JOIN `semester_courses`
                    ON `seminare`.`Seminar_id` = `semester_courses`.`course_id`
                ";
        if ($institut_id === 'all' && $GLOBALS['perm']->have_perm('root')) {
            $sql .= "WHERE 1 {$seminare_condition} ";
        } elseif ($all == 'all') {
            $sql .= "INNER JOIN Institute USING (Institut_id)
                    WHERE Institute.fakultaets_id = :faculty_id {$seminare_condition}
                    ";
            $parameters['faculty_id'] = $institut_id;
        } else {
            $sql .= "WHERE seminare.Institut_id = :institute_id {$seminare_condition}
                    ";
            $parameters['institute_id'] = $institut_id;
        }
        $sql .= "GROUP BY `seminare`.`Seminar_id` ORDER BY `seminar_courseset`.`set_id`, `seminare`.`Name`";

        return Course::findBySql($sql, $parameters);
    }

    function get_institutes($seminare_condition)
    {
        global $perm, $user;

        // Prepare institute statement
        $query = "SELECT a.Institut_id, a.Name, COUNT(courseset_rule.type) AS count
        FROM Institute AS a
        LEFT JOIN seminare ON (seminare.Institut_id = a.Institut_id)
        LEFT JOIN semester_courses ON (seminare.Seminar_id = semester_courses.course_id)
        LEFT JOIN seminar_courseset on seminar_courseset.seminar_id=seminare.seminar_id
        LEFT JOIN courseset_rule ON courseset_rule.type='ParticipantRestrictedAdmission' AND seminar_courseset.set_id=courseset_rule.set_id
        WHERE fakultaets_id = ?
            AND a.Institut_id != fakultaets_id
            {$seminare_condition}
        GROUP BY a.Institut_id
        ORDER BY a.Name, count DESC";
        $institute_statement = DBManager::get()->prepare($query);

        $parameters = [];
        if ($perm->have_perm('root')) {
            $query = "SELECT COUNT(*) FROM courseset_rule
                      INNER JOIN seminar_courseset on seminar_courseset.set_id=courseset_rule.set_id
                      INNER JOIN seminare ON (seminar_courseset.seminar_id = seminare.seminar_id)
                      LEFT JOIN semester_courses ON (seminare.Seminar_id = semester_courses.course_id)
                      WHERE courseset_rule.type='ParticipantRestrictedAdmission'
                          {$seminare_condition}";
            $statement = DBManager::get()->query($query);
            $num_sem = $statement->fetchColumn();

            $_my_inst['all'] = [
                'name'    => _('alle'),
                'is_fak'  => true,
                'count' => $num_sem,
            ];
            $query = "SELECT a.Institut_id, a.Name, 1 AS is_fak, COUNT(courseset_rule.type) AS count
            FROM Institute AS a
            LEFT JOIN seminare ON (seminare.Institut_id = a.Institut_id)
            LEFT JOIN semester_courses ON (seminare.Seminar_id = semester_courses.course_id)
            LEFT JOIN seminar_courseset on seminar_courseset.seminar_id=seminare.seminar_id
            LEFT JOIN courseset_rule ON courseset_rule.type='ParticipantRestrictedAdmission' AND seminar_courseset.set_id=courseset_rule.set_id
            WHERE a.Institut_id = fakultaets_id
                {$seminare_condition}
            GROUP BY a.Institut_id
            ORDER BY is_fak, Name, count DESC";
        } else {
            $query = "SELECT s.inst_perms,b.Institut_id, b.Name, b.Institut_id = b.fakultaets_id AS is_fak, COUNT( courseset_rule.type ) AS count
            FROM user_inst AS s
            LEFT JOIN Institute AS b USING ( Institut_id )
            LEFT JOIN seminare ON ( seminare.Institut_id = b.Institut_id)
            LEFT JOIN semester_courses ON (seminare.Seminar_id = semester_courses.course_id)
            LEFT JOIN seminar_courseset on seminar_courseset.seminar_id=seminare.seminar_id
            LEFT JOIN courseset_rule ON courseset_rule.type='ParticipantRestrictedAdmission' AND seminar_courseset.set_id=courseset_rule.set_id
            WHERE s.user_id = ?
                AND s.inst_perms IN ('admin', 'dozent')
                {$seminare_condition}
            GROUP BY b.Institut_id
            ORDER BY is_fak, Name, count DESC";
            $parameters[] = $user->id;
        }
        $statement = DBManager::get()->prepare($query);
        $statement->execute($parameters);
        $temp = $statement->fetchAll(PDO::FETCH_ASSOC);
        $_my_inst = [];
        foreach ($temp as $row) {
            if (!isset($_my_inst[$row['Institut_id']])) {
                $_my_inst[$row['Institut_id']] = [];
            }
            $_my_inst[$row['Institut_id']] = [
                'name'   => $row['Name'],
                'is_fak' => $row['is_fak'],
                'count'  => $row['count'],
            ];
            if ($row['is_fak'] && (!isset($row['inst_perms']) || $row['inst_perms'] !== 'dozent')) {
                $institute_statement->execute([$row['Institut_id']]);
                $alle = $institute_statement->fetchAll();
                if (count($alle)) {
                    $_my_inst[$row['Institut_id'] . '_all'] = [
                        'name'   => sprintf(_('[Alle unter %s]'), $row['Name']),
                        'is_fak' => 'all',
                        'count'  => $row['count']
                    ];

                    $num_inst = 0;
                    $num_sem_alle = $row['count'];

                    foreach ($alle as $institute) {
                        if(empty($_my_inst[$institute['Institut_id']])) {
                            $num_inst += 1;
                            $num_sem_alle += $institute['count'];
                        }
                        $_my_inst[$institute['Institut_id']] = [
                            'name'   => $institute['Name'],
                            'is_fak' => 0,
                            'count'  => $institute['count']
                        ];
                    }
                    $_my_inst[$row['Institut_id']]['num_inst']          = $num_inst;
                    $_my_inst[$row['Institut_id'] . '_all']['num_inst'] = $num_inst;
                    $_my_inst[$row['Institut_id'] . '_all']['count']    = $num_sem_alle;
                }
            }
        }
        return $_my_inst;
    }
}
