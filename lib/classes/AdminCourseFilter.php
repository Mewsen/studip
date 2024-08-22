<?php

/**
 * Class AdminCourseFilter
 *
 * The main class to filter all courses for admins. It's a singleton class, so you
 * better call it with AdminCourseFilter::get(). The whole class is created to
 * provide a nice hook for plugins to add special filters into the admin-area of
 * Stud.IP.
 *
 * To add a filter with a plugin, listen to the notification "AdminCourseFilterWillQuery"
 * like this:
 *
 *     NotificationCenter::addObserver($this, "addMyFilter", "AdminCourseFilterWillQuery");
 *
 * Where $this is an object and "addMyFilter" a method. Such a method might look like this:
 *
 *     public function addLectureshipFilter($event, $filter)
 *     {
 *         if ($GLOBALS['user']->cfg->getValue("LECTURESHIP_FILTER")) {
 *             $filter->query->join('lehrauftrag', 'seminare.Seminar_id = lehrauftrag.seminar_id');
 *         }
 *     }
 *
 * Within this method you alter the public $filter->query object. That query object is of type SQLQuery.
 *
 */
class AdminCourseFilter
{
    protected static $instance = null;

    /** @var SQLQuery|null  */
    public $query = null;
    public $max_show_courses = 500;
    public $settings = [];

    /**
     * returns an AdminCourseFilter singleton object
     * @return AdminCourseFilter or derived-class object
     */
    public static function get($reset_settings = false)
    {
        if ($reset_settings) {
            return new static($reset_settings);
        }

        if (!self::$instance) {
            self::$instance = new static($reset_settings);
        }
        return self::$instance;
    }

    /**
     * Constructor of the singleton-object.
     */
    public function __construct(bool $reset_settings = false)
    {
        $this->initSettings($reset_settings);
    }

    protected function initSettings(bool $reset_settings = false): void
    {
        $this->query = SQLQuery::table('seminare');
        $this->query->join('sem_types', 'sem_types', 'sem_types.id = seminare.status');
        $this->query->join('sem_classes', 'sem_classes', 'sem_classes.id = sem_types.class');
        $this->query->where("sem_classes.studygroup_mode = '0'");
        $this->query->groupBy('seminare.Seminar_id');

        if (!$reset_settings && $GLOBALS['user']->cfg->ADMIN_COURSES_SEARCHTEXT) {
            $this->query->join('teachers_su', 'seminar_user', "teachers_su.Seminar_id = seminare.Seminar_id AND teachers_su.status = 'dozent'");
            $this->query->join('teachers', 'auth_user_md5', 'teachers.user_id = teachers_su.user_id');
            $this->query->where(
                'search',
                "(seminare.name LIKE :search OR seminare.VeranstaltungsNummer LIKE :search OR seminare.untertitel LIKE :search OR CONCAT(teachers.Vorname, ' ', teachers.Nachname) LIKE :search)",
                ['search' => '%'.$GLOBALS['user']->cfg->ADMIN_COURSES_SEARCHTEXT.'%']
            );
        }
        if (!$reset_settings && Request::option('course_id')) {
            $this->query->where('course_id', 'seminare.Seminar_id = :course_id', ['course_id' => Request::option('course_id')]);
        }
        $inst_ids = [];

        if (
            !$GLOBALS['user']->cfg->MY_INSTITUTES_DEFAULT
            || $GLOBALS['user']->cfg->MY_INSTITUTES_DEFAULT === 'all'
        ) {
            $inst = new SimpleCollection(Institute::getMyInstitutes($GLOBALS['user']->id));
            $inst_ids = $inst->map(function ($a) {
                return $a['Institut_id'];
            });
        } else {

            $inst_id = $GLOBALS['user']->cfg->MY_INSTITUTES_DEFAULT;
            $inst_ids[] = $inst_id;

            if ($GLOBALS['user']->cfg->MY_INSTITUTES_INCLUDE_CHILDREN) {
                $inst = Institute::find($inst_id);
                if ($inst && $inst->isFaculty()) {
                    foreach ($inst->sub_institutes->pluck('Institut_id') as $institut_id) {
                        $inst_ids[] = $institut_id;
                    }
                }
            }
        }

        if (Config::get()->ALLOW_ADMIN_RELATED_INST) {
            $this->query->where('seminar_inst', 'EXISTS (SELECT 1 FROM seminar_inst WHERE seminar_id = seminare.Seminar_id AND institut_id IN (:institut_ids))');
        } else {
            $this->query->where("seminar_inst", "seminare.institut_id IN (:institut_ids)");
        }
        $this->query->parameter('institut_ids', $inst_ids);

        if ($GLOBALS['user']->cfg->MY_COURSES_SELECTED_CYCLE) {
            $this->query->join('semester_courses', 'semester_courses.course_id = seminare.Seminar_id', 'LEFT JOIN');
            $this->query->where('semester_id', '(semester_courses.semester_id = :semester_id OR semester_courses.semester_id IS NULL)', [
                'semester_id' => $GLOBALS['user']->cfg->MY_COURSES_SELECTED_CYCLE
            ]);
        }

        if (
            $GLOBALS['user']->cfg->MY_COURSES_TYPE_FILTER &&
            $GLOBALS['user']->cfg->MY_COURSES_TYPE_FILTER !== 'all'
        ) {
            if (str_contains($GLOBALS['user']->cfg->MY_COURSES_TYPE_FILTER, '_')) {
                list($sem_class_id, $sem_type_id) = explode('_', $GLOBALS['user']->cfg->MY_COURSES_TYPE_FILTER);
                $this->query->where('course_type', 'seminare.status = :course_type', ['course_type' => $sem_type_id]);
            } else {
                //sem class
                $this->query->where('course_class', 'sem_types.class = :course_class', [
                    'course_class' => $GLOBALS['user']->cfg->MY_COURSES_TYPE_FILTER
                ]);
            }

        }

        if ($GLOBALS['user']->cfg->MY_COURSES_SELECTED_STGTEIL) {
            $this->query->join('mvv_lvgruppe_seminar', '`mvv_lvgruppe_seminar`.`seminar_id` = `seminare`.`Seminar_id`');
            $this->query->join('mvv_lvgruppe_modulteil', '`mvv_lvgruppe_modulteil`.`lvgruppe_id` = `mvv_lvgruppe_seminar`.`lvgruppe_id`');
            $this->query->join('mvv_modulteil', '`mvv_modulteil`.`modulteil_id` = `mvv_lvgruppe_modulteil`.`modulteil_id`');
            $this->query->join('mvv_modul', '`mvv_modul`.`modul_id` = `mvv_modulteil`.`modul_id`');
            $this->query->join('mvv_stgteilabschnitt_modul', '`mvv_stgteilabschnitt_modul`.`modul_id` = `mvv_modul`.`modul_id`');
            $this->query->join('mvv_stgteilabschnitt', '`mvv_stgteilabschnitt`.`abschnitt_id` = `mvv_stgteilabschnitt_modul`.`abschnitt_id`');
            $this->query->join('mvv_stgteilversion', '`mvv_stgteilversion`.`version_id` = `mvv_stgteilabschnitt`.`version_id`');
            $this->query->where('stgteil', 'mvv_stgteilversion.stgteil_id = :stgteil_id', [
                'stgteil_id' => $GLOBALS['user']->cfg->MY_COURSES_SELECTED_STGTEIL
            ]);
        }

        if ($GLOBALS['user']->cfg->ADMIN_COURSES_TEACHERFILTER) {
            $this->query->join('teachers_su', 'seminar_user', "teachers_su.Seminar_id = seminare.Seminar_id AND teachers_su.status = 'dozent'");
            $this->query->where(
                'teacher_filter',
                "teachers_su.user_id = :teacher_id",
                ['teacher_id' => $GLOBALS['user']->cfg->ADMIN_COURSES_TEACHERFILTER]
            );
        }

        if (!$reset_settings) {
            $datafields_filters = $GLOBALS['user']->cfg->ADMIN_COURSES_DATAFIELDS_FILTERS;
            foreach ($datafields_filters as $datafield_id => $value) {
                $this->query->join('de_'.$datafield_id, 'datafields_entries', 'de_'.$datafield_id.'.range_id = seminare.Seminar_id AND `de_'.$datafield_id.'`.datafield_id = :de_'.$datafield_id.'_id');
                $this->query->where('de_' . $datafield_id . '_contents', 'de_' . $datafield_id . '.`content` LIKE :de_' . $datafield_id . '_content',
                    [
                        'de_' . $datafield_id . '_id' => $datafield_id,
                        'de_' . $datafield_id . '_content' => '%' . $value . '%'
                    ]);
            }
        }
    }

    /**
     * Returns the data of the resultset of the AdminCourseFilter.
     * Also saves the settings in the session.
     * Note that a notification AdminCourseFilterWillQuery will be posted, before the result is computed.
     * Plugins may register at this event to fully alter this AdminCourseFilter-object and so the resultset.
     * @return array associative array with seminar_ids as keys and seminar-data-arrays as values.
     */
    public function getCourses()
    {
        NotificationCenter::postNotification("AdminCourseFilterWillQuery", $this);
        return $this->query->fetchAll(Course::class);
    }

    /**
     * @return integer number of courses that this filter would return
     */
    public function countCourses()
    {
        NotificationCenter::postNotification("AdminCourseFilterWillQuery", $this);
        return $this->query->count();
    }

    /**
     * @param int|null $limit
     * @return Course[]
     */
    public function fetchCourses(?int $limit = null): array
    {
        NotificationCenter::postNotification('AdminCourseFilterWillQuery', $this);
        return $this->query->fetchAll(Course::class, $limit);
    }

    /**
     * Returns the data of the resultset of the AdminCourseFilter.
     *
     * @param string $order_by possible values name or number
     *
     * Note that a notification AdminCourseFilterWillQuery will be posted, before the result is computed.
     * Plugins may register at this event to fully alter this AdminCourseFilter-object and so the resultset.
     * @return array associative array with seminar_ids as keys and seminar-data-arrays as values.
     */
    public function getCoursesForAdminWidget(string $order_by = 'name')
    {
        try {
            $order = 'seminare.name';
            if ($order_by === 'number') {
                $order = 'seminare.veranstaltungsnummer, seminare.name';
            }
            $this->query->orderBy($order);
            return $this->fetchCourses($this->max_show_courses);
        } catch (OverflowException $e) {
            return [];
        }
    }

}
