<?php
class MyStudygroupsController extends AuthenticatedController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        if (!$GLOBALS['perm']->have_perm('root')) {
            Navigation::activateItem('/browse/my_studygroups/index');
        }
    }

    public function index_action($is_widget = false)
    {
        PageLayout::setHelpKeyword('Basis.MeineStudiengruppen');
        PageLayout::setTitle(_('Meine Studiengruppen'));
        URLHelper::removeLinkParam('cid');

        $this->is_widget    = (bool)$is_widget;
        $this->studygroups  = StudygroupModel::getStudygroups();
        $this->nav_elements = MyRealmModel::calc_single_navigation($this->studygroups);

        // do not render sidebar if this is the widget
        if (!$this->is_widget) {
            $this->set_sidebar();
        }
    }

    public function proposals_action()
    {
        PageLayout::setHelpKeyword('Basis.MeineStudiengruppen');
        PageLayout::setTitle(_('Meine Studiengruppen'));
        URLHelper::removeLinkParam('cid');
        $this->proposed_studygroups = $this->proposeStudygroups();
    }

    public function set_sidebar()
    {
        if ($GLOBALS['user']->perms === 'user') {
            return;
        }

        $sidebar = Sidebar::Get();

        $actions = new ActionsWidget();
        $actions->addLink(
            _('Neue Studiengruppe anlegen'),
            URLHelper::getURL('dispatch.php/course/wizard', ['studygroup' => 1]),
            Icon::create('add')
        )->asDialog('size=auto');
        if (count($this->studygroups) > 0) {
            $actions->addLink(
                _('Farbgruppierung ändern'),
                URLHelper::getURL('dispatch.php/my_courses/groups/all/true'),
                Icon::create('group4')
            )->asDialog();
        }
        $sidebar->addWidget($actions);
    }

    public function proposeStudygroups($user_id = null, $amount = 4)
    {
        $user_id ??= User::findCurrent()->id;
        $cache_id = 'core/studygroups/proposals/' . $user_id;
        $cache = \Studip\Cache\Factory::getCache();
        $studygroup_ids = $cache->read($cache_id);
        if ($studygroup_ids !== false) {
            return  Course::findMany($studygroup_ids);
        }

        // Vorgeschlagen werden sollen Studiengruppen,
        // a) in denen Personen sitzen, die auch in anderen Veranstaltungen sitzen, in denen der aktive Nutzer Mitglied ist
        // b) die zu dem Studienbereich des Studierenden gehören
        // c) die einfach neu sind
        // und die zudem aktiv sind. Es wird eine Liste von 36 Studiengruppen gebaut, wovon drei alle 15 Minuten im Widget
        // angezeigt werden.

        $studygroup_sem_types = array_filter(
            array_keys($GLOBALS['SEM_TYPE']),
            function ($sem_type_id) {
                return (bool) $GLOBALS['SEM_CLASS'][$GLOBALS['SEM_TYPE'][$sem_type_id]['class']]['studygroup_mode'];
            }
        );

        $statement = DBManager::get()->prepare("
            SELECT `Seminar_id` FROM (
                SELECT `seminare`.`Seminar_id`, COUNT(`seminar_user`.`user_id`) AS `count_colleages`
                FROM  `seminar_user` AS `my_courses`
                    LEFT JOIN `seminar_user` AS `my_colleages` ON (`my_colleages`.`Seminar_id` = `my_courses`.`Seminar_id`)
                    LEFT JOIN `seminar_user` ON (`my_colleages`.`user_id` = `seminar_user`.`user_id`)
                    LEFT JOIN `seminar_user` AS `am_i_connected` ON (`seminar_user`.`Seminar_id` = `am_i_connected`.`Seminar_id` AND `am_i_connected`.`user_id` = :me)
                    LEFT JOIN `seminare` ON (`seminare`.`Seminar_id` = `seminar_user`.`Seminar_id`)
                WHERE `seminare`.`status` IN (:studygroup_types)
                    AND `am_i_connected`.`user_id` IS NULL
                    AND `my_courses`.`user_id` = :me
                GROUP BY `seminare`.`seminar_id`
                ORDER BY `count_colleages` DESC
                LIMIT 12
            ) AS `colleages_groups`

            UNION SELECT `Seminar_id` FROM (
                SELECT `seminare`.`Seminar_id`
                FROM `seminare`
                    LEFT JOIN `seminar_user` AS `am_i_connected` ON (`am_i_connected`.`Seminar_id` = `seminare`.`Seminar_id` AND `am_i_connected`.`user_id` = :me)
                    INNER JOIN `studygroup_stgteil` ON (`studygroup_stgteil`.`studygroup_id` = `seminare`.`Seminar_id`)
                    INNER JOIN `mvv_stgteil` ON (`studygroup_stgteil`.`stgteil_id` = `mvv_stgteil`.`stgteil_id`)
                    INNER JOIN `user_studiengang` ON (`user_studiengang`.`fach_id` = `mvv_stgteil`.`fach_id`)
                    INNER JOIN `mvv_stg_stgteil` ON (`mvv_stg_stgteil`.`stgteil_id` = `mvv_stgteil`.`stgteil_id`)
                    INNER JOIN `mvv_studiengang` ON (`mvv_studiengang`.`studiengang_id` = `mvv_stg_stgteil`.`studiengang_id`
                                                         AND `mvv_studiengang`.`abschluss_id` = `user_studiengang`.`abschluss_id`)
                WHERE `am_i_connected`.`user_id` IS NULL
                    AND `seminare`.`status` IN (:studygroup_types)
                    AND `user_studiengang`.`user_id` = :me
                ORDER BY rand()
                LIMIT 12
            ) AS `same_studyarea_groups`

            UNION SELECT `Seminar_id` FROM (
                SELECT `seminare`.`Seminar_id`
                FROM `seminare`
                    LEFT JOIN `seminar_user` AS `am_i_connected` ON (`am_i_connected`.`Seminar_id` = `seminare`.`Seminar_id` AND `am_i_connected`.`user_id` = :me)
                WHERE `am_i_connected`.`user_id` IS NULL
                    AND `seminare`.`status` IN (:studygroup_types)
                ORDER BY `seminare`.`mkdate` DESC
                LIMIT 12
            ) AS `new_groups`

            GROUP BY `Seminar_id`
            ORDER BY rand()
            LIMIT :amount
        ");
        $statement->execute([
            'studygroup_types' => $studygroup_sem_types,
            'me' => $user_id,
            'amount' => $amount
        ]);
        $group_ids = $statement->fetchAll(PDO::FETCH_COLUMN);
        $cache->write($cache_id, $group_ids, 15 * 60);
        return Course::findMany($group_ids);
    }
}
