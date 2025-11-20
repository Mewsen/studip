<?php
namespace MassWidget;

use DBManager;
use JSONArrayObject;
use Plugin;
use Semester;
use SimpleORMap;
use User;
use UserFilter;
use UserFilterRange;
use WidgetUser;

class MassWidget extends SimpleORMap implements UserFilterRange
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'masswidget';

        $config['serialized_fields']['settings'] = JSONArrayObject::class;

        $config['has_one']['author'] = [
            'class_name' => User::class,
            'foreign_key' => 'author_id',
            'assoc_foreign_key' => 'user_id'
        ];
        $config['has_many']['filters'] = [
            'class_name' => MassWidgetFilter::class,
            'assoc_foreign_key' => 'masswidget_id',
            'on_store' => 'store',
            'on_delete' => 'delete'
        ];
        $config['has_one']['plugin'] = [
            'class_name' => Plugin::class,
            'foreign_key' => 'plugin_id',
            'assoc_foreign_key' => 'pluginid'
        ];

        parent::configure($config);
    }

    public static function getTargets(): array
    {
        return [
            'all' => _('alle'),
            'students' => _('Studierende'),
            'employees' => _('Mitarbeitende'),
            'lecturers' => _('Aktive Lehrende'),
            'courses' => _('Veranstaltungen'),
            'usernames' => _('Liste von Nutzernamen'),
        ];
    }

    /**
     * Gets the real recipient list for this widget.
     * @return string[] The list of user IDs that will receive this widget.
     */
    public function getTargetUserIds(): array
    {
        $ids = [];

        switch ($this->target) {
            // Everyone studying something or working at an institute.
            case 'all':
                $students = DBManager::get()->fetchFirst("SELECT DISTINCT `user_id` FROM `user_studiengang`");

                $employees = DBManager::get()->fetchFirst(
                    "SELECT DISTINCT `user_id` FROM `user_inst` WHERE `inst_perms` IN (:perms)",
                    ['perms' => ['autor', 'tutor', 'dozent']]
                );

                $ids = array_unique(array_merge($students, $employees));

                break;

            // Students are users with at least one studycourse assignment in user_studiengang.
            case 'students':
                $ids = DBManager::get()->fetchFirst("SELECT DISTINCT `user_id` FROM `user_studiengang`");

                if (count($this->filters) > 0) {
                    $filtered = [];
                    foreach ($this->filters as $filter) {
                        $f = new UserFilter($filter->filter_id);
                        $filtered = array_merge($filtered, $f->getUsers());
                    }

                    $ids = array_unique(array_intersect($ids, $filtered));
                }

                break;

            // Employees are users with at least one institute assignment at 'autor" level or more.
            case 'employees':
                $ids = DBManager::get()->fetchFirst(
                    "SELECT DISTINCT `user_id` FROM `user_inst` WHERE `inst_perms` IN (:perms)",
                    ['perms' => ['autor', 'tutor', 'dozent']]
                );

                if (count($this->filters) > 0) {
                    $filtered = [];
                    foreach ($this->filters as $filter) {
                        $f = new UserFilter($filter->filter_id);
                        $filtered = array_merge($filtered, $f->getUsers());
                    }

                    $ids = array_unique(array_intersect($ids, $filtered));
                }

                break;

            // Course members having the specified permission level.
            case 'courses':
                $courses = array_map(
                    fn ($course) => $course['id'],
                    $this->settings['courses']->getArrayCopy()
                );
                $permission = $this->settings['perm'];

                $ids = DBManager::get()->fetchFirst(
                    "SELECT DISTINCT `user_id` FROM `seminar_user` WHERE `Seminar_id` IN (:courses) AND `status` = :perm",
                    ['courses' => $courses, 'perm' => $permission]
                );

                break;

            // Lecturers of at least one course in the given semester
            case 'lecturers':

                $ids = DBManager::get()->fetchFirst(
                    "SELECT DISTINCT u.`user_id` FROM `seminar_user` u
                        LEFT JOIN `semester_courses` sc ON (sc.`course_id` = u.`Seminar_id`)
                        JOIN `seminare` s ON (s.`Seminar_id` = u.`Seminar_id`)
                        JOIN `sem_types` t ON (t.`id` = s.`status`)
                    WHERE (sc.`semester_id` = :semester OR sc.`semester_id` IS NULL)
                        AND t.`class` IN (:categories)
                        AND u.`status` = 'dozent'",
                    [
                        'semester' => $this->settings['semester'],
                        'categories' => \Config::get()->MASSMAIL_LECTURER_SEM_CATEGORIES
                    ]
                );

                break;

            case 'usernames':

                $ids = DBManager::get()->fetchFirst(
                    "SELECT DISTINCT `user_id` FROM `auth_user_md5` WHERE `Username` IN (:usernames)",
                    ['usernames' => explode("\n", $this->settings['usernames'])]
                );
        }

        return DBManager::get()->fetchFirst(
            "SELECT DISTINCT `user_id`
                FROM `auth_user_md5`
                WHERE `visible` != :visible
                    AND `locked` = :locked
                    AND `user_id` IN (:ids)
                    AND `username` NOT IN (:exclude)
                ORDER BY `username`
            ",
            [
                'visible' => 'never',
                'locked' => 0,
                'ids' => $ids,
                'exclude' => $this->exclude_users ? explode("\n", $this->exclude_users) : ['']
            ]
        );
    }

    public function deleteUserWidgets(): self
    {
        $recipientIds = $this->getTargetUserIds();

        WidgetUser::deleteBySQL(
            'pluginid = :plugin_id AND range_id IN (:user_ids)',
            ['plugin_id' => $this->plugin_id, 'user_ids' => $recipientIds]
        );

        return $this;
    }

    public function canEditFilter(User $user, UserFilter $filter): bool
    {
        return $GLOBALS['perm']->have_perm('root');
    }
}
