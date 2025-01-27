<?php

class Course_ConnectedcoursesController extends AuthenticatedController
{

    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        if (!$GLOBALS['perm']->have_studip_perm('tutor', Context::getId())) {
            throw new AccessDeniedException();
        }
    }

    public function index_action()
    {
        Navigation::activateItem('/course/admin/connectedcourses');
        $this->connected = StudygroupCourse::findBySQL(
            'INNER JOIN seminare ON (seminare.Seminar_id = studygroup_courses.course_id) WHERE studygroup_courses.studygroup_id = ? ORDER BY seminare.name ASC',
            [
                Context::getId()
            ]
        );
        $this->proposals = StudygroupCourseProposal::findBySQL(
            'INNER JOIN seminare ON (seminare.Seminar_id = studygroup_courses_proposals.studygroup_id) WHERE studygroup_courses_proposals.studygroup_id = ? ORDER BY seminare.name ASC',
            [
                Context::getId()
            ]
        );
        $this->buildSidebar();

    }

    public function connect_action($course_id = null)
    {

        Navigation::activateItem('/course/admin/connectedcourses');
        PageLayout::setTitle(_('Veranstaltung suchen und zur Verknüpfung vorschlagen'));

        if (Request::isPost() && (Request::option('course_id') || $course_id)) {
            CSRFProtection::verifySecurityToken();
            $course_id = $course_id ?? Request::option('course_id');
            $status = StudygroupModel::proposeAsStudygroupTo(Context::get(), $course_id);
            if ($status === 'connected') {
                PageLayout::postSuccess(_('Veranstaltung wurde verknüpft.'));
            }
            if ($status === 'proposed') {
                PageLayout::postSuccess(_('Vorschlag wurde eingereicht.'));
            }
            $this->redirect('course/connectedcourses/index');
            return;
        }

        $connected = StudygroupCourse::findBySQL(
            'INNER JOIN seminare ON (seminare.Seminar_id = studygroup_courses.course_id) WHERE studygroup_courses.studygroup_id = ? ORDER BY seminare.name ASC',
            [
                Context::getId()
            ]
        );
        $proposals = StudygroupCourseProposal::findBySQL(
            'INNER JOIN seminare ON (seminare.Seminar_id = studygroup_courses_proposals.studygroup_id) WHERE studygroup_courses_proposals.studygroup_id = ? ORDER BY seminare.name ASC',
            [
                Context::getId()
            ]
        );
        $already_covered = array_map(function($c) { return $c->course_id; }, $connected);
        $already_covered = $already_covered + array_map(function($c) { return $c->course_id; }, $proposals);




        $studygroup_ids = [];
        foreach (SemClass::getClasses() as $sem_class) {
            if ($sem_class['studygroup_mode'] > 0) {
                foreach ($sem_class->getSemTypes() as $sem_type) {
                    $studygroup_ids[] = $sem_type['id'];
                }
            }
        }
        $this->my_courses = [];
        if (!$GLOBALS['perm']->have_perm('admin')) {
            $this->my_courses = Course::findBySQL('INNER JOIN `seminar_user` USING (`Seminar_id`)
                    LEFT JOIN `semester_courses` ON (`seminare`.`Seminar_id` = `semester_courses`.`course_id`)
                    WHERE `seminar_user`.`user_id` = :user_id
                        AND `seminare`.`status` NOT IN (:studygroup_sem_types)
                        AND (`semester_courses`.`semester_id` IS NULL OR `semester_courses`.`semester_id` = :semester_id)
                        AND `seminare`.`Seminar_id` NOT IN (:ignore)
                    ORDER BY `seminare`.`name` ASC ',
                [
                    'user_id' => User::findCurrent()->id,
                    'studygroup_sem_types' => $studygroup_ids,
                    'semester_id' => Request::get('semester_id') ?? Semester::findCurrent()->id,
                    'ignore' => count($already_covered) ? $already_covered : ''
                ]);
            foreach ($this->my_courses as $my_course) {
                $already_covered[] = $my_course->id;
            }
        }

        if (Request::get('search') && Request::get('search') != 1) {
            //do the search:
            $query = SQLQuery::table('seminare')
                ->where('search',
                    '`name` LIKE :search OR `VeranstaltungsNummer` LIKE :search',
                    ['search' => '%' . Request::get('search') . '%']
                )
                ->where(
                    'studygroups',
                    '`seminare`.`status` NOT IN (:sem_type_ids)',
                    ['sem_type_ids' => $studygroup_ids]
                )
                ->groupBy('`seminare`.`Seminar_id`');
            if (count($already_covered) > 0) {
                $query->where(
                    'ignore',
                    '`seminare`.`Seminar_id` NOT IN (:ignore)',
                    ['ignore' => $already_covered]
                );
            }
            if (!empty(Request::get('semester_id'))) {
                $query->join(
                    'semester_courses',
                    'semester_courses',
                    '`semester_courses`.`course_id` = `seminare`.`Seminar_id`',
                    'LEFT JOIN'
                );
                $query->where(
                    'semester_id',
                    'semester_courses.semester_id = :semester_id OR semester_courses.semester_id IS NULL',
                    ['semester_id' => Request::get('semester_id')]
                );
            }
            $this->searchresults = $query->fetchAll(Course::class);
        } else {
            //get up to 10 courses with a lot of members of the current studygroup:
            $statement = DBManager::get()->prepare("
                SELECT `seminare`.*
                FROM `seminar_user`
                    INNER JOIN `seminare` ON (`seminare`.`Seminar_id` = `seminar_user`.`Seminar_id`)
                    INNER JOIN `seminar_user` AS `su2` ON (`su2`.`user_id` = `seminar_user`.`user_id` AND `su2`.`Seminar_id` = :course_id)
                    LEFT JOIN `studygroup_courses` ON (`studygroup_courses`.`course_id` = `seminare`.`Seminar_id` AND `studygroup_courses`.`studygroup_id` = `su2`.`Seminar_id`)
                WHERE `seminare`.`status` NOT IN (:studygroup_sem_types)
                    AND `studygroup_courses`.`id` IS NULL
                    AND `seminare`.`Seminar_id` NOT IN (:ignore)
                GROUP BY `seminare`.`Seminar_id`
                HAVING COUNT(`seminar_user`.`user_id`) > 1
                ORDER BY COUNT(`seminar_user`.`user_id`) DESC
                LIMIT 20
            ");
            $statement->execute([
                'course_id' => Context::getId(),
                'studygroup_sem_types' => $studygroup_ids,
                'ignore' => count($already_covered) ? $already_covered : ''
            ]);
            $suggestions = $statement->fetchAll(PDO::FETCH_ASSOC);
            $this->suggestions = array_map(function ($d) {
                return Course::buildExisting($d);
            }, $suggestions);
        }


    }

    public function remove_action($course_id)
    {
        if (Request::isPost() && $course_id) {
            CSRFProtection::verifySecurityToken();
            StudygroupCourse::deleteBySQL('course_id = ? AND studygroup_id = ?', [
                $course_id,
                Context::getId()
            ]);
            PageLayout::postSuccess(_('Verknüpfung zu der Veranstaltung wurde aufgehoben.'));
        }
        $this->redirect('course/connectedcourses/index');
    }

    public function decline_action(StudygroupCourseProposal $proposal)
    {
        if (Request::isPost()) {
            CSRFProtection::verifySecurityToken();
            if ($GLOBALS['perm']->have_studip_perm('tutor', $proposal['course_id']) || $GLOBALS['perm']->have_studip_perm('tutor', $proposal['studygroup_id'])) {
                if ($proposal['proposed_from'] === 'course') {
                    PageLayout::postSuccess(_('Vorschlag wurde abgewiesen.'));
                    $statement = DBManager::get()->prepare("
                        SELECT `username`, `user_id`
                        FROM `auth_user_md5`
                            INNER JOIN `seminar_user` USING (`user_id`)
                        WHERE `seminar_user`.`Seminar_id` = ? AND `seminar_user`.`status` IN ('tutor', 'dozent')
                    ");
                    $statement->execute([$proposal['course_id']]);
                    $messaging = new messaging();

                    foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $user_data) {
                        setTempLanguage($user_data['user_id']);
                        $messaging->insert_message(
                            sprintf(
                                _('Ihr Vorschlag, die Studiengruppe „%1$s“ mit der Veranstaltung „%2$s“ zu verknüpfen, wurde leider abgewiesen.'),
                                Context::get()->getFullname(),
                                $proposal->studygroup->getFullname()
                            ),
                            $user_data['username'],
                            '____%system%____',
                            '',
                            '',
                            '',
                            '',
                            _('Verknüpfungsvorschlag abgewiesen'),
                            '',
                            'normal',
                            ['Studiengruppe']
                        );
                        restoreLanguage();
                    }
                } else {
                    PageLayout::postSuccess(_('Vorschlag wurde zurückgezogen.'));
                }
                $proposal->delete();
            }
        }
        $this->redirect('course/connectedcourses/index');
    }

    protected function buildSidebar()
    {
        $actions = new ActionsWidget();
        $actions->addLink(
            _('Verknüpfung vorschlagen'),
            $this->url_for('course/connectedcourses/connect'),
            Icon::create('add'),
            ['data-dialog' => 1]
        );
        Sidebar::Get()->addWidget($actions);
    }
}
