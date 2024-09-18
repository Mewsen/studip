<?php

require_once __DIR__.'/../courseware_controller.php';

use Courseware\StructuralElement;
use Courseware\Unit;

class Contents_CoursewareController extends CoursewareController
{
    /**
     * Callback function being called before an action is executed.
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function before_filter(&$action, &$args): void
    {
        parent::before_filter($action, $args);

        PageLayout::setHelpKeyword('Basis.Courseware'); // set keyword for new help

        PageLayout::setTitle(_('Courseware'));

        $this->user = $GLOBALS['user'];
        $this->licenses = $this->getLicenses();
        $this->oer_enabled = Config::get()->OERCAMPUS_ENABLED && $GLOBALS['perm']->have_perm(Config::get()->OER_PUBLIC_STATUS);
        $this->unitsNotFound = Unit::countBySql('range_id = ?', [$this->user->id]) === 0;
    }

    /**
     * Entry point of the controller that displays the courseware projects overview
     *
     * @param string $action
     * @param string $widgetId
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function index_action(): void
    {
        Navigation::activateItem('/contents/courseware/shelf');
        $this->user_id = $GLOBALS['user']->id;
        $this->setShelfSidebar();
    }

    private function setShelfSidebar(): void
    {
        $sidebar = Sidebar::Get();
        $sidebar->addWidget(new VueWidget('courseware-action-widget'));
        SkipLinks::addIndex(_('Aktionen'), 'courseware-action-widget', 21);
    }

    /**
     * Show Courseware of current user
     *
     * @param string $action
     * @param string $widgetId
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function courseware_action($unit_id = null): void
    {
        global $user;

        Navigation::activateItem('/contents/courseware/courseware');
        if ($this->unitsNotFound) {
            PageLayout::postMessage(MessageBox::info(_('Es wurde kein Lernmaterial gefunden.')));
            return;
        }

        $this->setCoursewareSidebar();

        $this->user_id = $user->id;
        /** @var array<mixed> $last */
        $last = UserConfig::get($this->user_id)->getValue('COURSEWARE_LAST_ELEMENT');

        if (empty($unit_id)) {
            $this->redirectToFirstUnit('user', $this->user_id, $last);

            return;
        }

        $this->entry_element_id = null;
        $this->unit_id = null;
        $unit = Unit::find($unit_id);
        if (isset($unit)) {
            $this->setEntryElement('user', $unit, $last, $this->user_id);
        }
    }

    /**
     * Show users bookmarks
     *
     * @param string $action
     * @param string $widgetId
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */

    public function bookmarks_action(): void
    {
        Navigation::activateItem('/contents/courseware/bookmarks');
        $this->user_id = $GLOBALS['user']->id;
        $this->setBookmarkSidebar();
    }

    /**
     * Show users releases
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */

    public function releases_action(): void
    {
        Navigation::activateItem('/contents/courseware/releases');
        $this->user_id = $GLOBALS['user']->id;
    }

    private function setBookmarkSidebar(): void
    {
        $sidebar = Sidebar::Get();
        $views = new TemplateWidget(
            _('Filter'),
            $this->get_template_factory()->open('contents/courseware/bookmark_filter_widget')
        );
        $sidebar->addWidget($views)->addLayoutCSSClass('courseware-bookmark-filter-widget');
    }

    /**
     * displays coursewares in courses
     *
     * @param string $action
     * @param string $widgetId
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function courses_overview_action($action = false, $widgetId = null): void
    {
        Navigation::activateItem('/contents/courseware/courses_overview');

        $sidebar = Sidebar::get();
        $semester_widget = new SemesterSelectorWidget(
            $this->url_for('contents/courseware/courses_overview')
        );
        $semester_widget->includeAll();
        $sidebar->addWidget($semester_widget);

        $this->user_id = $GLOBALS['user']->id;

        $sem_key = Request::get('semester_id');
        if ($sem_key === '0' || $sem_key === null) {
            $sem_key = 'all';
            $this->all_semesters = true;
            $this->semesters = Semester::getAll();
        } else {
            $this->all_semesters = false;
            $this->semesters = [Semester::find($sem_key)];
        }
        usort($this->semesters, function ($a, $b) {
            if ($a->beginn === $b->beginn) {
                return 0;
            }
            return ($a->beginn > $b->beginn) ? -1 : 1;
        });

        $this->sem_courses  = $this->getCoursewareCourses($sem_key);
    }

    /**
     * Return list of coursewares grouped by semester_id
     *
     * @param  string $sem_key  currently selected semester or all (for all semesters)
     *
     * @return array
     */
    private function getCoursewareCourses($sem_key): array
    {
        $this->current_semester = Semester::findCurrent();

        $courses = Course::findThru($this->user_id, [
            'thru_table'        => 'seminar_user',
            'thru_key'          => 'user_id',
            'thru_assoc_key'    => 'seminar_id',
            'assoc_foreign_key' => 'seminar_id'
        ]);

        if (Config::get()->DEPUTIES_ENABLE) {
            $deputy_courses = Deputy::findDeputyCourses($GLOBALS['user']->id)->pluck('course');
            if (!empty($deputy_courses)) {
                $courses = array_merge($courses, $deputy_courses);
            }
        }

        $courses = new SimpleCollection($courses);

        if (!Config::get()->MY_COURSES_ENABLE_STUDYGROUPS) {
            $courses = $courses->filter(function (Course $course) {
                return !$course->isStudygroup();
            });
        }

        // Filter courses with enabled and visible courseware
        $courses = $courses->filter(function (Course $course) {
            return $this->isCoursewareEnabledAndVisible($course);
        });

        if ($sem_key !== 'all') {
            $semester = Semester::find($sem_key);

            $courses = $courses->filter(function (Course $course) use ($semester) {
                return $course->isInSemester($semester);
            });
        }
        $sem_courses = [];
        foreach ($courses as $course) {
            $units = Unit::findCoursesUnits($course);
            foreach ($units as $unit) {
                $element = $unit->structural_element;
                if (!$element || !$element->canRead(User::findCurrent())) {
                    continue;
                }

                $element['payload'] = json_decode($element['payload'], true);

                if ($course->isOpenEnded()) {
                    $sem_courses[$this->current_semester->id]['coursewares'][] = $element;
                } else {
                    $end_semester = $course->getEndSemester();
                    $sem_courses[$end_semester->id]['coursewares'][] = $element;
                }
            }
        }

        return $sem_courses;
    }

    /**
     * Returns true if the courseware module is enabled and visible for the
     *  passed course and current user
     *
     * @param  Course  $course  the course to check
     * @return boolean true if courseware is enabled and visible,
     *                 false otherwise
     */
    private function isCoursewareEnabledAndVisible(Course $course): bool
    {
        // Check if courseware is globally enabled
        $studip_module = PluginManager::getInstance()->getPlugin(CoursewareModule::class);
        if (!$studip_module) {
            return false;
        }

        // Check if courseware is enabled in course
        $active_tool = ToolActivation::find([
            $course->id,
            $studip_module->getPluginId(),
        ]);
        if (!$active_tool) {
            return false;
        }

        // Check visibility
        return $GLOBALS['perm']->have_studip_perm(
            $active_tool->getVisibilityPermission(),
            $course->id,
            User::findCurrent()->id
        );
    }

}
