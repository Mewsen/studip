<?php
final class MyCoursesHelper
{
    public function createVueAppData(string $sem_key, string $group_field = 'sem_number'): array
    {
        return $this->getVueAppData(
            $this->getCourses($sem_key, $group_field),
            $group_field
        );
    }

    public function getCourses(string $sem_key, string $group_field = 'sem_number'): array
    {
        return MyRealmModel::getPreparedCourses($sem_key, [
            'group_field'         => $group_field,
            'order_by'            => null,
            'order'               => 'asc',
            'studygroups_enabled' => Config::get()->MY_COURSES_ENABLE_STUDYGROUPS,
            'deputies_enabled'    => Config::get()->DEPUTIES_ENABLE,
        ]);
    }

    /**
     * Get the data array for presenting the course list in Vue.
     *
     * @param array|null $sem_courses
     * @param string $group_field
     * @return array{
     *     courses: array,
     *     groups: array,
     *     user_id: string,
     *     config: array{
     *         allow_dozent_visibility: bool,
     *         open_groups: array,
     *         sem_number: bool,
     *         display_type: string,
     *         responsive_type: string,
     *         navigation_show_only_new: bool,
     *         group_by: string
     *     }
     * }
     */
    public function getVueAppData(?array $sem_courses, string $group_field = 'sem_number'): array
    {
        $sem_data = Semester::getAllAsArray();
        $temp_courses = [];
        $groups = [];

        if (is_array($sem_courses)) {
            foreach ($sem_courses as $_outer_index => $_outer) {
                if ($group_field === 'sem_number') {
                    $_courses = [];

                    foreach ($_outer as $course) {
                        if(!empty($course['sem_number'])) {
                            $_courses[$course['seminar_id']] = $course;
                        }

                        if (!empty($course['children']) && is_array($course['children'])) {
                            foreach ($course['children'] as $child) {
                                $_courses[$child['seminar_id']] = $child;
                            }
                        }
                    }

                    if ($_outer_index) {
                        $groups[] = [
                            'id' => $_outer_index,
                            'name' => (string)$sem_data[$_outer_index]['name'],
                            'data' => [
                                [
                                    'id' => md5($_outer_index),
                                    'label' => false,
                                    'ids' => array_keys($_courses),
                                ],
                            ],
                        ];
                    }
                    $temp_courses = array_merge($temp_courses, $_courses);
                } else {
                    $count = 1;
                    $_groups = [];
                    foreach ($_outer as $_inner_index => $_inner) {
                        $_courses = [];

                        foreach ($_inner as $course) {
                            $_courses[$course['seminar_id']] = $course;
                            if (isset($course['children']) && is_array($course['children'])) {
                                foreach ($course['children'] as $child) {
                                    $_courses[$child['seminar_id']] = $child;
                                }
                            }
                        }

                        $label = $_inner_index;
                        if ($group_field === 'sem_tree_id' && !$label) {
                            $label = _('keine Zuordnung');
                        } elseif ($group_field === 'gruppe') {
                            $label = _('Gruppe') . ' ' . $count++;
                        }

                        $_groups[] = [
                            'id' => md5($_outer_index . $_inner_index),
                            'label' => $label,
                            'ids' => array_keys($_courses),
                        ];

                        $temp_courses = array_merge($temp_courses, $_courses);
                    }

                    if ($_outer_index) {
                        $groups[] = [
                            'id' => $_outer_index,
                            'name' => (string)$sem_data[$_outer_index]['name'],
                            'data' => $_groups,
                        ];
                    }
                }
            }
        }

        return [
            'setCourses' => $this->sanitizeNavigations(array_map([$this, 'convertCourse'], $temp_courses)),
            'setGroups'  => $groups,
            'setUserId'  => User::findCurrent()->id,
            'setConfig'  => [
                'allow_dozent_visibility'  => Config::get()->getValue('ALLOW_DOZENT_VISIBILITY'),
                'open_groups'              => array_values(User::findCurrent()->getConfiguration()->getValue('MY_COURSES_OPEN_GROUPS')),
                'sem_number'               => Config::get()->getValue('IMPORTANT_SEMNUMBER'),
                'sem_number_always'        => Config::get()->getValue('MY_COURSES_ALWAYS_SHOW_SEMNUM'),
                'view_settings'            => User::findCurrent()->getConfiguration()->getValue('MY_COURSES_VIEW_SETTINGS'),
                'group_by'                 => $group_field,
            ],
        ];
    }

    private function sanitizeNavigations(array $courses): array
    {
        // Count occurences of slots
        $counters = [];
        foreach ($courses as $course) {
            foreach ($course['navigation'] as $key => $value) {
                if (!isset($counters[$key])) {
                    $counters[$key] = 0;
                }
                if ($value) {
                    $counters[$key] += 1;
                }
            }
        }

        // Detect which slots are not set at all
        $remove = array_keys(array_filter($counters, function ($counter) {
            return !$counter;
        }));

        // Set positions by predefined positions without the always empty slots
        $positions = array_diff(array_keys(MyRealmModel::getDefaultModules()), $remove);

        // Get other positions based on count
        arsort($counters);
        foreach ($counters as $key => $count) {
            if ($count && !in_array($key, $positions)) {
                $positions[] = $key;
            }
        }

        // Sort and filter course navigations
        return array_map(
            function ($course) use ($positions) {
                $course['navigation'] = array_filter($course['navigation'], function ($key) use ($positions) {
                    return in_array($key, $positions);
                }, ARRAY_FILTER_USE_KEY);
                uksort($course['navigation'], function ($a, $b) use ($positions) {
                    return array_search($a, $positions) - array_search($b, $positions);
                });
                $course['navigation'] = array_values($course['navigation']);
                return $course;
            },
            $courses
        );
    }

    private function convertCourse($course)
    {
        $is_teacher = !empty($course['user_status']) && in_array($course['user_status'], ['tutor', 'dozent']);

        $avatar = !empty($course['sem_class']['studygroup_mode'])
            ? StudygroupAvatar::getAvatar($course['seminar_id'])
            : CourseAvatar::getAvatar($course['seminar_id']);

        $extra_navigation = false;
        if ($is_teacher) {
            $adminmodule = $course['sem_class']->getAdminModuleObject();
            if ($adminmodule) {
                $adminnavigation = $adminmodule->getIconNavigation($course['seminar_id'], 0, $GLOBALS['user']->id);
                $extra_navigation = [
                    'url'   => URLHelper::getURL($adminnavigation->getURL(), ['cid' => $course['seminar_id']]),
                    'icon'  => $adminnavigation->getImage()->getShape(),
                    'label' => $adminnavigation->getLinkAttributes()['title'] ?? _('Verwaltung'),
                ];
            }
        }

        if (!empty($course['children']) && empty($course['seminar_id'])) {
            foreach ($course['children'] as $_course) {
                return [
                    'id'                => (string) $_course['seminar_id'],
                    'name'              => (string) $_course['name'],
                    'number'            => (string) $_course['veranstaltungsnummer'],
                    'group'             => !empty($_course['gruppe']) ? (int) $_course['gruppe'] : '',
                    'admission_binding' => !empty($_course['admission_binding']),
                    'children'          => array_column($_course['children'] ?? [], 'seminar_id'),
                    'parent'            => $_course['parent_course'] ?? null,

                    'is_teacher'    => !empty($_course['user_status']) && in_array($_course['user_status'], ['tutor', 'dozent']),
                    'is_studygroup' => !empty($_course['sem_class']['studygroup_mode']),
                    'is_hidden'     => empty($_course['visible']),
                    'is_deputy'     => !empty($_course['is_deputy']),
                    'is_group'      => !empty($_course['is_group']),

                    'avatar' => $avatar->getURL(Avatar::MEDIUM),

                    'navigation'       => $this->reduceNavigation($_course['navigation'] ?? null),
                    'extra_navigation' => $extra_navigation,
                ];
            }
        }
        return [
            'id'                => (string) $course['seminar_id'],
            'name'              => (string) $course['name'],
            'number'            => (string) $course['veranstaltungsnummer'],
            'group'             => !empty($course['gruppe']) ? (int) $course['gruppe'] : '',
            'admission_binding' => !empty($course['admission_binding']),
            'children'          => array_column($course['children'] ?? [], 'seminar_id'),
            'parent'            => $course['parent_course'] ?? null,

            'is_teacher'    => !empty($course['user_status']) && in_array($course['user_status'], ['tutor', 'dozent']),
            'is_studygroup' => !empty($course['sem_class']['studygroup_mode']),
            'is_hidden'     => empty($course['visible']),
            'is_deputy'     => !empty($course['is_deputy']),
            'is_group'      => !empty($course['is_group']),

            'avatar' => $avatar->getURL(Avatar::MEDIUM),

            'navigation'       => $this->reduceNavigation($course['navigation'] ?? null),
            'extra_navigation' => $extra_navigation,
        ];
    }

    private function reduceNavigation($nav): array
    {
        if (!$nav) {
            return [];
        }

        $result = [];
        foreach (MyRealmModel::array_rtrim($nav) as $key => $n) {
            if (!$n || !$n->isVisible(true)) {
                $item = false;
            } else {
                $attr = $n->getLinkAttributes();
                if (empty($attr['title']) && $n->getImage()) {
                    $attr['title'] = (string) ($n->getImage()->getAttributes()['title'] ?? '');
                }
                if (empty($attr['title'])) {
                    $attr['title'] = (string) $n->getTitle();
                }
                $attr['title'] = (string) $attr['title'];

                $item = [
                    'url'       => $n->getURL(),
                    'icon'      => $this->convertIcon($n->getImage()),
                    'attr'      => $attr,
                    'important' => $n->getImage()->signalsAttention(),
                ];
            }
            $result[$key] = $item;
        }

        return $result;
    }

    /**
     * @return array{role: string, shape: string}
     */
    private function convertIcon(Icon $icon): array
    {
        return [
            'role' => $icon->getRole(),
            'shape' => $icon->getShape(),
        ];
    }
}
