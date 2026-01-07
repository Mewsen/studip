<?php

final class CreateForumHelpTour extends Migration
{

    function description()
    {
        return 'Adds a help tour for the new forum and links it in the system';
    }

    public function up()
    {
        // Create entries for forum tour.
        DBManager::get()->exec(
            "INSERT IGNORE INTO `help_tours` VALUES (
                '33fa547967cfa9b7edab321f5d8ca744',
                'ea68d2f9d7b81d01d2d3ea38a105c734',
                'Forum - Der Einstieg',
                '6.1 - Die Tour durch das noch leere Forum.',
                'tour',
                'autor,tutor,dozent,admin,root',
                1,
                'de',
                '6.1',
                '',
                '',
                UNIX_TIMESTAMP(),
                UNIX_TIMESTAMP()
            )"
        );
        DBManager::get()->exec(
            "INSERT IGNORE INTO `help_tour_settings` VALUES (
                'ea68d2f9d7b81d01d2d3ea38a105c734',
                1,
                'autostart_once',
                UNIX_TIMESTAMP(),
                UNIX_TIMESTAMP()
            )"
        );

        $steps = [
            [
                'step' => 1,
                'title' => 'Willkommen!',
                'tip' => 'Hier ist ein Ort für Diskussionen, Ankündigungen und gemeinsames Arbeiten.',
                'orientation' => 'B',
                'css_selector' => '#nav_course_forum > a:nth-child(1)',
                'route' => 'dispatch.php/course/forum/topics',
            ],
            [
                'step' => 2,
                'title' => 'Diskussion starten',
                'tip' => 'Beginnen Sie im Aktionsmenü eine Diskussion zu einem bestehenden oder neuen Thema.',
                'orientation' => 'R',
                'css_selector' => '#link-430746eaab49d0ff0bcac73c2bc7a0a7 > a:nth-child(1)',
                'route' => 'dispatch.php/course/forum/topics',
            ],
            [
                'step' => 3,
                'title' => 'Themenübersicht',
                'tip' => 'Ein Thema ist ein Überordner, der mehrere Diskussionen enthält. Dies ist zugleich die Startseite des Forums.',
                'orientation' => 'R',
                'css_selector' => '#nav_forum_topics',
                'route' => 'dispatch.php/course/forum/topics',
            ],
            [
                'step' => 4,
                'title' => 'Alle Diskussionen',
                'tip' => 'Hier finden Sie eine Übersicht aller stattfindenden Diskussionen.',
                'orientation' => 'R',
                'css_selector' => '#nav_forum_discussions',
                'route' => 'dispatch.php/course/forum/topics',
            ],
            [
                'step' => 5,
                'title' => 'Letzte Aktivität',
                'tip' => 'Die neuesten Beiträge finden Sie, indem Sie alle Diskussionen absteigend nach „Letzte Aktivität“ sortieren.',
                'orientation' => 'B',
                'css_selector' => '.sortdesc',
                'route' => 'dispatch.php/course/forum/discussions',
            ],
            [
                'step' => 6,
                'title' => 'Abonnements',
                'tip' => 'Welche Themen und Beiträge verfolgen Sie?',
                'orientation' => 'R',
                'css_selector' => '#nav_forum_subscriptions',
                'route' => 'dispatch.php/course/forum/topics',
            ],
            [
                'step' => 7,
                'title' => 'Kategorien',
                'tip' => 'Sie fassen mehrere Themen zusammen und sind optional.',
                'orientation' => 'B',
                'css_selector' => '#nav_forum_categories',
                'route' => 'dispatch.php/course/forum/topics',
            ],
            [
                'step' => 8,
                'title' => 'Tour & Hilfe',
                'tip' => 'Diese Tour und weitere Hilfeseiten finden Sie hier.',
                'orientation' => 'B',
                'css_selector' => '#helpbar_icon > svg:nth-child(1)',
                'route' => 'dispatch.php/course/forum/topics',
            ],
            [
                'step' => 9,
                'title' => 'Auf in den Austausch!',
                'tip' => 'Worüber wollen Sie diskutieren?',
                'orientation' => 'B',
                'css_selector' => 'a.button--icon-label',
                'route' => 'dispatch.php/course/forum/topics',
            ]
        ];
        $stmt = DBManager::get()->prepare(
            "INSERT IGNORE INTO `help_tour_steps`
            VALUES
            (
                 :tour_id,
                 :step,
                 :title,
                 :tip,
                 :orientation,
                 :interactive,
                 :css_selector,
                 :route,
                 :action_prev,
                 :action_next,
                 :author_email,
                 :mkdate,
                 :chdate
             )"
        );
        $meta = [
            'tour_id' => 'ea68d2f9d7b81d01d2d3ea38a105c734',
            'interactive' => 0,
            'action_prev' => '',
            'action_next' => '',
            'author_email' => '',
            'mkdate' => time(),
            'chdate' => time()
        ];
        foreach ($steps as $step) {
            $stmt->execute(array_merge($meta, $step));
        }
    }

    public function down()
    {
        $tour = ['tour_id' => 'ea68d2f9d7b81d01d2d3ea38a105c734'];
        DBManager::get()->execute("DELETE FROM `help_tours` WHERE `tour_id` = :tour_id", $tour);
        DBManager::get()->execute("DELETE FROM `help_tour_steps` WHERE `tour_id` = :tour_id", $tour);
        DBManager::get()->execute("DELETE FROM `help_tour_settings` WHERE `tour_id` = :tour_id", $tour);
    }

}
