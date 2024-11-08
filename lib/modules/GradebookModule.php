<?php

use Grading\Definition;
use Grading\Instance;

/**
 * GradebookModule.php - Gradebook API for Stud.IP.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      <mlunzena@uos.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 */
class GradebookModule extends CorePlugin implements SystemPlugin, StudipModuleExtended
{
    public function __construct()
    {
        parent::__construct();

        NotificationCenter::on('UserDidDelete', function ($event, $user) {
            Instance::deleteBySQL('user_id = ?', [$user->id]);
        });
        NotificationCenter::on('CourseDidDelete', function ($event, $course) {
            Definition::deleteBySQL('course_id = ?', [$course->id]);
        });
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getInfoTemplate($courseId)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getIconNavigation($courseId, $lastVisit, $userId)
    {
        if ($userId === 'nobody') {
            return null;
        }

        $title = _('Gradebook');
        if ($GLOBALS['perm']->have_studip_perm('tutor', $courseId, $userId)) {
            $changed = Instance::countBySQL(
                'INNER JOIN grading_definitions gd ON(gd.id = definition_id) '.
                'WHERE gd.course_id = ? AND grading_instances.chdate > ?',
                [$courseId, $lastVisit]
            );
        } else {
            $changed = Instance::countBySQL(
                'INNER JOIN grading_definitions gd ON(gd.id = definition_id) '.
                'WHERE gd.course_id = ? AND grading_instances.chdate > ? AND user_id = ?',
                [$courseId, $lastVisit, $userId]
            );
        }

        $icon = $changed
              ? Icon::create('assessment', Icon::ROLE_NEW)
              : Icon::create('assessment', Icon::ROLE_CLICKABLE);

        $navigation = new Navigation($title, 'dispatch.php/course/gradebook/overview');
        $navigation->setImage($icon->copyWithAttributes(['title' => $title]));

        return $navigation;
    }

    public function getManyIconNavigation(array $course_ids, array $visits, string $user_id = null): array
    {
        if ($user_id === 'nobody') {
            return [];
        }
        // split courses in student-perms and tutor-perms
        $tutor_c_ids = [];
        foreach ($course_ids as $course_id) {
            if ($GLOBALS['perm']->have_studip_perm('tutor', $course_id, $user_id)) {
                $tutor_c_ids[$course_id] = $course_id;
            }
        }
        $results = DBManager::get()->fetchGrouped(
            "SELECT gd.course_id, gi.user_id
            FROM grading_instances gi
            INNER JOIN grading_definitions gd ON(gd.id = definition_id)
            LEFT JOIN object_user_visits AS ouv
              ON ouv.object_id = gd.course_id
                AND ouv.user_id = :user_id
                AND ouv.plugin_id = :plugin_id
            WHERE gd.course_id IN (:course_ids) AND gi.chdate > IFNULL(ouv.visitdate, :threshold)",
            [
                ':user_id' => $user_id,
                ':plugin_id' => $this->getPluginId(),
                ':course_ids' => $course_ids,
                ':threshold' => object_get_visit_threshold(),
            ]
        );

        $title = _('Gradebook');
        $navs = [];
        foreach ($course_ids as $course_id) {
            if (empty($results[$course_id])) {
                $changed = false;
            } elseif (isset($tutor_c_ids[$course_id])) {
                $changed = count($results[$course_id]);
            } else {
                $filtered_results = array_filter($results[$course_id], fn ($fetched_user_id) => $fetched_user_id === $user_id);
                $changed = !empty($filtered_results) ? count($filtered_results) : 0;
            }
            $icon = $changed
                ? Icon::create('assessment', Icon::ROLE_NEW)
                : Icon::create('assessment', Icon::ROLE_CLICKABLE);
            $navigation = new Navigation($title, 'dispatch.php/course/gradebook/overview');
            $navigation->setImage($icon->copyWithAttributes(['title' => $title]));
            $navs[$course_id] = $navigation;
        }

        return $navs;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getTabNavigation($cid)
    {
        if ('nobody' === $GLOBALS['user']->id) {
            return [];
        }

        $gradebook = new Navigation('Gradebook');
        $gradebook->addSubNavigation('index', new Navigation(_('Erbrachte Leistungen'), 'dispatch.php/course/gradebook/overview'));

        if ($GLOBALS['perm']->have_studip_perm('tutor', $cid)) {
            $this->addTabNavigationOfLecturers($gradebook, $cid);
        }

        return compact('gradebook');
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function addTabNavigationOfLecturers(\Navigation $navigation, $cid)
    {
        $navigation->addSubNavigation(
            'weights',
            new Navigation(_('Gewichtungen'), 'dispatch.php/course/gradebook/lecturers/weights')
        );
        $navigation->addSubNavigation(
            'edit_custom_definitions',
            new Navigation(_('Manuelle Leistungen definieren'), 'dispatch.php/course/gradebook/lecturers/edit_custom_definitions')
        );
        $navigation->addSubNavigation(
            'custom_definitions',
            new Navigation(_('Noten manuell erfassen'), 'dispatch.php/course/gradebook/lecturers/custom_definitions')
        );
        if (Config::get()->ILIAS_INTERFACE_ENABLE && Course::find($cid)->isToolActive('IliasInterfaceModule')) {
            $navigation->addSubNavigation(
                'edit_ilias_definitions',
                new Navigation(_('ILIAS-Test als Leistung definieren'), 'dispatch.php/course/gradebook/lecturers/edit_ilias_definitions')
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function exportUserData(StoredUserData $storage)
    {
        if ($instances = Grading\Instance::findBySql('user_id = ?', [$storage->user_id])) {
            $fieldData = array_map(
                function ($instance) {
                    return
                        array_merge(
                            $instance->definition->toRawArray('course_id item name tool category weight'),
                            $instance->toRawArray('rawgrade feedback mkdate chdate')
                        );
                },
                $instances
            );
            if ($fieldData) {
                $storage->addTabularData(_('Leistungen'), 'fach', $fieldData);
            }
        }
    }

    /**
     * Provides metadata like a descriptional text for this module that
     * is shown on the course "+" page to inform users about what the
     * module acutally does. Additionally, a URL can be specified.
     *
     * @return array metadata containg description and/or url
     */
    public function getMetadata()
    {
        return [
            'summary' => _('Noten- und Fortschrittserfassung (Gradebook)'),
            'description' => _('Dieses Modul ermöglicht die manuelle und automatische Erfassung von Noten und Leistungen.'),
            'category' => _('Lehr- und Lernorganisation'),
            'keywords' => _('automatische und manuelle Erfassung von gewichteten Leistungen;Export von Leistungen;persönliche Fortschrittskontrolle'),
            'icon' => Icon::create('assessment', Icon::ROLE_INFO),
            'icon_clickable' => Icon::create('assessment', Icon::ROLE_CLICKABLE),
            'screenshots' => [
                'path' => 'assets/images/plus/screenshots/Gradebook',
                'pictures' => [
                    [
                        'source' => 'Lehrendensicht.png',
                        'title' => 'Beispiel für das Gradebook aus der Sicht der Lehrenden',
                    ],
                    [
                        'source' => 'Studierendensicht.png',
                        'title' => 'Beispiel für das Gradebook aus der Sicht der Studierenden',
                    ],
                ],
            ],
        ];
    }

    public function isActivatableForContext(Range $context)
    {
        return $context->getRangeType() === 'course';
    }
}
