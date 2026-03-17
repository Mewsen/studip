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
            return Course::findMany($studygroup_ids);
        }

        // Vorgeschlagen werden sollen Studiengruppen,
        // a) in denen Personen sitzen, die auch in anderen Veranstaltungen sitzen, in denen der aktive Nutzer Mitglied ist
        // b) die zu dem Studienbereich des Studierenden gehören
        // c) die einfach neu sind
        // und die zudem aktiv sind. Es wird eine Liste von 36 Studiengruppen gebaut, wovon drei alle 15 Minuten im Widget
        // angezeigt werden.

        $studygroup_sem_types = array_filter(
            array_keys($GLOBALS['SEM_TYPE']),
            fn($sem_type_id) => (bool) $GLOBALS['SEM_CLASS'][$GLOBALS['SEM_TYPE'][$sem_type_id]['class']]['studygroup_mode']
        );

        $query = "SELECT DISTINCT `Seminar_id`
                  FROM (
                    SELECT `Seminar_id` FROM (
                      -- Andere Personen aus meinen Veranstaltungen
                      SELECT `seminare`.`Seminar_id`, COUNT(`seminar_user`.`user_id`) AS `count_colleagues`
                      FROM (
                        SELECT colleagues.`user_id`
                        FROM `seminar_user` AS colleagues
                        JOIN `seminar_user` AS mine USING (`Seminar_id`)
                        WHERE mine.`user_id` = :me
                          AND colleagues.`user_id` != mine.`user_id`
                      ) AS my_colleagues
                      JOIN `seminar_user`
                        ON (`my_colleagues`.`user_id` = `seminar_user`.`user_id`)
                      JOIN `seminare`
                        ON (`seminare`.`Seminar_id` = `seminar_user`.`Seminar_id`)
                      WHERE `seminare`.`status` IN (:studygroup_types)
                        AND NOT EXISTS(
                          SELECT 1
                          FROM `seminar_user`
                          WHERE `seminar_user`.`Seminar_id` = `seminare`.`Seminar_id`
                            AND `seminar_user`.`user_id` = :me
                        )
                      GROUP BY `seminare`.`seminar_id`
                      ORDER BY `count_colleagues` DESC
                      LIMIT 12
                    ) AS `colleagues_groups`

                    UNION ALL

                    SELECT `Seminar_id` FROM (
                      -- Andere Personen aus meinen Studiengängen
                      SELECT DISTINCT `seminare`.`Seminar_id`
                      FROM `user_studiengang`
                      STRAIGHT_JOIN `mvv_stgteil`
                        ON (`mvv_stgteil`.`fach_id` = `user_studiengang`.`fach_id`)
                      STRAIGHT_JOIN `mvv_stg_stgteil`
                        ON (`mvv_stg_stgteil`.`stgteil_id` = `mvv_stgteil`.`stgteil_id`)
                      STRAIGHT_JOIN `mvv_studiengang`
                        ON (
                          `mvv_studiengang`.`studiengang_id` = `mvv_stg_stgteil`.`studiengang_id`
                          AND `mvv_studiengang`.`abschluss_id` = `user_studiengang`.`abschluss_id`
                        )
                      STRAIGHT_JOIN `studygroup_stgteil`
                        ON (`studygroup_stgteil`.`stgteil_id` = `mvv_stgteil`.`stgteil_id`)
                      STRAIGHT_JOIN `seminare`
                        ON (`seminare`.`Seminar_id` = `studygroup_stgteil`.`studygroup_id`)
                      WHERE `seminare`.`status` IN (:studygroup_types)
                        AND `user_studiengang`.`user_id` = :me
                        AND NOT EXISTS(
                          SELECT 1
                          FROM `seminar_user`
                          WHERE `seminar_user`.`Seminar_id` = `seminare`.`Seminar_id`
                            AND `seminar_user`.`user_id` = :me
                        )
                      LIMIT 12
                    ) AS `same_studyarea_groups`

                    UNION ALL

                    SELECT `Seminar_id` FROM (
                      -- Neue Studiengruppen
                      SELECT `seminare`.`Seminar_id`
                      FROM `seminare`
                      WHERE `seminare`.`status` IN (:studygroup_types)
                        AND NOT EXISTS(
                          SELECT 1
                          FROM `seminar_user`
                          WHERE `seminar_user`.`Seminar_id` = `seminare`.`Seminar_id`
                            AND `seminar_user`.`user_id` = :me
                        )
                      ORDER BY `seminare`.`mkdate` DESC
                      LIMIT 12
                    ) AS `new_groups`
                ) AS `all_groups`

                LIMIT :amount";
        $group_ids = DBManager::get()->fetchFirst($query, [
            ':studygroup_types' => $studygroup_sem_types,
            ':me'               => $user_id,
            ':amount'           => $amount,
        ]);

        // Zufällig sortieren ist in PHP schneller als in SQL
        $group_ids = shuffle($group_ids);

        $cache->write($cache_id, $group_ids, 15 * 60);
        return Course::findMany($group_ids);
    }
}
