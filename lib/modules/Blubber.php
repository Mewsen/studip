<?php
/*
 *  Copyright (c) 2012-2019  Rasmus Fuhse <fuhse@data-quest.de>
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 */

/**
 * Class Blubber - the Blubber-plugin
 * This is only used to manage blubber within a course.
 */
class Blubber extends CorePlugin implements StudipModuleExtended
{
    use IconNavigationTrait;

    /**
     * Returns a navigation for the tab displayed in the course.
     * @param string $course_id of the course
     * @return \Navigation
     */
    public function getTabNavigation($course_id)
    {
        $tab = new Navigation(
            _('Blubber'),
            'dispatch.php/course/messenger/course'
        );
        $tab->setImage(Icon::create('blubber', Icon::ROLE_INFO_ALT));
        return ['blubber' => $tab];
    }

    public function getManyIconNavigation(array $course_ids, ?string $user_id = null): array
    {
        $user_id = $user_id ?? User::findCurrent()->id;
        $threshold = object_get_visit_threshold();

        // check if there are comments newer than the last visit of blubber
        $condition = "JOIN blubber_threads USING (thread_id)
                      LEFT JOIN object_user_visits AS ouv
                        ON ouv.object_id = blubber_threads.context_id
                          AND ouv.user_id = :me
                          AND ouv.plugin_id = :plugin_id
                      WHERE blubber_threads.context_type = 'course'
                        AND blubber_threads.context_id IN (:course_ids)
                        AND blubber_comments.mkdate >= IF(ouv.visitdate > :threshold, ouv.visitdate, :threshold)
                        AND blubber_comments.user_id != :me
                        AND blubber_threads.visible_in_stream = 1";
        $params = [
            ':course_ids' => $course_ids,
            ':threshold' => $threshold,
            ':me' => $user_id,
            ':plugin_id' => 0, // module doesnt write directly into ouv
        ];
        $threads = [];
        BlubberComment::findEachBySQL(
            function ($comment) use (&$threads) {
                $threads[$comment->thread_id][] = $comment;
            },
            $condition,
            $params
        );

        $navs = [];
        foreach ($threads as $comments) {
            $thread = $comments[0]->thread;
            if (isset($navs[$thread->context_id])) {
                continue;
            }
            // check if there are comments that are newer thant the last visit of the blubber thread(!)
            if ($thread->isReadable() && $thread->getLatestActivity() > $thread->getLastVisit()) {
                $nav = new Navigation(_('Blubber'), 'dispatch.php/course/messenger/course', ['thread' => 'new']);
                $nav->setImage(Icon::create('blubber', Icon::ROLE_ATTENTION));
                $nav->setLinkAttributes(['title' => _('Es gibt neue Blubber')]);
                $nav->setBadgeNumber(count($comments));
                $navs[$thread->context_id] = $nav;
            }
        }

        // Check for the remaining Courses, if new threads were created
        $remaining_courses = array_diff($course_ids, array_keys($navs));
        $condition = "LEFT JOIN object_user_visits AS ouv
                        ON ouv.object_id = blubber_threads.context_id
                          AND ouv.user_id = :me
                          AND ouv.plugin_id = :plugin_id
                      WHERE blubber_threads.context_type = 'course'
                        AND blubber_threads.context_id IN (:course_ids)
                        AND blubber_threads.mkdate >= IF(ouv.visitdate > :threshold, ouv.visitdate, :threshold)
                        AND blubber_threads.user_id != :me
                        AND blubber_threads.visible_in_stream = 1
                        AND (
                            blubber_threads.display_class IS NOT NULL
                            OR blubber_threads.content IS NOT NULL
                        )";
        $threads = BlubberThread::findBySQL($condition, [
            ':course_ids'  => $remaining_courses,
            ':threshold' => $threshold,
            ':me' => $user_id,
            ':plugin_id' => 0, // module doesnt write directly into ouv
        ]);
        foreach ($threads as $thread) {
            if ($thread->isReadable()) {
                $nav = new Navigation(_('Blubber'), 'dispatch.php/course/messenger/course');
                $nav->setImage(Icon::create('blubber', Icon::ROLE_ATTENTION));
                $nav->setLinkAttributes(['title' => _('Es gibt neue Blubber')]);
                $nav->setTitle(_('Es gibt neue Blubber'));
                $navs[$thread->context_id] = $nav;
            }
        }

        $default_navigation = new Navigation(_('Blubber'), 'dispatch.php/course/messenger/course');
        $default_navigation->setImage(Icon::create('blubber'));
        $default_navigation->setLinkAttributes(['title' => _('Blubber-Messenger')]);
        foreach ($course_ids as $course_id) {
            if (!isset($navs[$course_id])) {
                $navs[$course_id] = $default_navigation;
            }
        }
        return $navs;
    }

    /**
     * Returns no template, because this plugin doesn't want to insert an
     * info-template in the course-overview.
     * @param string $course_id
     * @return null
     */
    public function getInfoTemplate($course_id)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        return [
            'displayname' => _('Blubber'),
            'summary' => _('Schneller Austausch von Informationen in Gesprächsform'),
            'description' => _('Blubber ist eine Kommunikationsform mit Ähnlichkeiten zu einem Forum, in dem aber in Echtzeit miteinander kommuniziert werden kann und das durch den etwas informelleren Charakter eher wie ein Chat anmutet. Anders als im Forum ist es nicht notwendig, die Seiten neu zu laden, um die neuesten Einträge (z. B. Antworten auf eigene Postings) sehen zu können: Die Seite aktualisiert sich selbst bei neuen Einträgen. Dateien (z.B. Fotos, Audiodateien, Links) können per Drag and Drop in das Feld gezogen und somit verlinkt werden. Auch Textformatierungen sind möglich.'),
            'descriptionlong' => _('Kommunikationsform mit Ähnlichkeiten zu einem Forum. Im Gegensatz zum Forum kann mit Blubber jedoch in Echtzeit miteinander kommuniziert werden. Das Tool ähnelt durch den etwas informelleren Charakter einem Messenger. Anders als im Forum ist es nicht notwendig, die Seiten neu zu laden, um die neuesten Einträge (z. B. Antworten auf eigene Postings) sehen zu können. Dateien (z. B. Fotos, Audiodateien, Links) können per drag and drop in das Feld gezogen und somit verlinkt werden. Auch Textformatierungen sind möglich.'),
            'category' => _('Kommunikation und Zusammenarbeit'),
            'keywords' => _('Einfach Text schreiben und mit <Enter> abschicken; Direktes Kontaktieren anderer Stud.IP-NutzerInnen (@Vorname Nachname); Setzen von und Suche nach Stichworten über Hashtags (#Stichwort); Einbinden von Dateien per drag and drop'),
            'icon' => Icon::create('blubber', Icon::ROLE_INFO),
            'icon_clickable' => Icon::create('blubber'),
            'screenshots' => [
                'path' => 'assets/images/plus/screenshots/Blubber',
                'pictures' => [
                    ['source' => 'Blubber.jpg', 'title' => 'Blubber']
                ]
            ]
        ];
    }
}
