<?php
/**
 * Forum: Discussion of specific topics within courses
 *
 * @author  Murtaza Sultani <sultani@data-quest.de>
 * @author  Rasmus Fuhse <fuhse@data-quest.de>
 * @license GPL2 or any later version
 * @since   Stud.IP 6.1
 */

use Forum\ForumPosting;

class CoreForum extends CorePlugin implements StudipModule
{
    public function getTabNavigation($course_id)
    {
        $navigation = new Navigation(_('Forum'), 'dispatch.php/course/forum/topics');

        $navigation->setImage(Icon::create('forum', 'info_alt'));

        $navigation->addSubNavigation(
            'topics',
            new Navigation(_('Themenübersicht'), 'dispatch.php/course/forum/topics')
        );

        if (!CourseConfig::get($course_id)->FORUM_HIDE_CATEGORIES_NAVIGATION) {
            $navigation->addSubNavigation(
                'categories',
                new Navigation(_('Kategorien'), 'dispatch.php/course/forum/categories')
            );
        }

        $navigation->addSubNavigation(
            'discussions',
            new Navigation(_('Alle Diskussionen'), 'dispatch.php/course/forum/discussions')
        );

        $navigation->addSubNavigation(
            'subscriptions',
            new Navigation(_('Abonnierte Diskussionen'), 'dispatch.php/course/forum/subscriptions')
        );

        return ['forum' => $navigation];
    }

    public function getIconNavigation($course_id, $last_visit, $user_id)
    {
        $recent_posts_count = 0;
        $navigation_title = _('Forum');

        if ($GLOBALS['perm']->have_studip_perm('user', $course_id)) {
            $recent_posts = ForumPosting::getRecentPosts($course_id, $last_visit);
            $recent_posts_count = array_sum(array_column($recent_posts, 'posts'));

            if ($recent_posts_count > 0) {
                $navigation_title = sprintf(_('%s neue Beiträge seit Ihrem letzten Besuch.'), $recent_posts_count);
            } else {
                $navigation_title = _('Keine neuen Beiträge seit Ihrem letzten Besuch.');
            }
        }

        $navigation = new Navigation(_("Forum"));
        $navigation->setBadgeNumber($recent_posts_count);

        $navigation->setLinkAttributes(['title' => $navigation_title]);

        if ($recent_posts_count > 0) {
            $navigation->setImage(Icon::create('forum', Icon::ROLE_ATTENTION));
            $navigation->setURL('dispatch.php/course/forum/recent', ['last_visit' => $last_visit]);
        } else {
            $navigation->setImage(Icon::create('forum'));
            $navigation->setURL('dispatch.php/course/forum/topics');
        }

        return $navigation;
    }

    public function getInfoTemplate($course_id)
    {
        // TODO: Implement getInfoTemplate() method.
        return null;
    }

    public static function isAdmin($course_id): bool
    {
        return $GLOBALS['perm']->have_perm('root') || $GLOBALS['perm']->have_studip_perm('dozent', $course_id);
    }

    public static function isModerator($course_id): bool
    {
        return self::isAdmin($course_id) ||
            CourseConfig::get($course_id)->FORUM_MODERATION_PERMISSION === $GLOBALS['perm']->get_studip_perm($course_id) ||
            CourseConfig::get($course_id)->FORUM_MODERATION_PERMISSION === 'all';
    }


    /**
     * {@inheritdoc}
     */
    public function getMetadata(): array
    {
        return [
            'summary' => _('Veranstaltungsbegleitender Meinungsaustausch zu bestimmten Themen'),
            'description' => _('Textbasierte zeit- und ortsunabhängige Möglichkeit zum Austausch von Gedanken, Meinungen und Erfahrungen. Lehrende und/oder Studierende können parallel zu Veranstaltungsthemen Fragen stellen, die in Form von Textbeiträgen besprochen werden können. Diese Beiträge können von allen Teilnehmenden der Veranstaltung gemerkt, verlinkt, positiv bewertet (sog. "Gefällt mir") und editiert werden (Letzeres nur von Lehrenden). Lehrende können zusätzlich Themen in Bereiche gliedern, zwischen den Bereichen verschiebe, im Bereich hervorheben und diesen öffnen und schließen.'),
            'descriptionlong' => _('Textbasierte zeit- und ortsunabhängige Möglichkeit zum Austausch von Gedanken, Meinungen und Erfahrungen. Lehrende und/oder Studierende können parallel zu Veranstaltungsthemen Fragen stellen, die in Form von Textbeiträgen besprochen werden können. Diese Beiträge können von allen Teilnehmenden der Veranstaltung gemerkt, verlinkt, positiv bewertet (sog. "Gefällt mir") und editiert werden (Letzeres nur von Lehrenden). Lehrende können zusätzlich Themen in Bereiche gliedern, zwischen den Bereichen verschieben, im Bereich hervorheben und diesen öffnen und schließen.'),
            'category' => _('Kommunikation und Zusammenarbeit'),
            'keywords' => _('Möglichkeit zum intensiven, nachhaltigen textbasierten Austausch; (nachträgliche) Strukturierung der Beiträge; Editierfunktion für Lehrende'),
            'icon' => Icon::create('forum', Icon::ROLE_INFO),
            'icon_clickable' => Icon::create('forum'),
            'screenshots' => [
                'path' => 'assets/images/plus/screenshots/Forum',
                'pictures' => [
                    ['source' => 'Lehrendensicht_-_Kategorien_mit_Bereichen_und_Beitraegen.jpg'],
                    ['source' => 'Studentische_Sicht_-_Kategorien_mit_Bereichen_und_Beitraegen.jpg'],
                    ['source' => 'Einen_Forumsbeitrag_erstellen.jpg'],
                ]
            ]
        ];
    }

    public static function deleteCourseContents($course_id): void
    {
        \Forum\ForumCategory::deleteBySQL("range_id = ?", [$course_id]);
        \Forum\ForumTopic::deleteBySQL("range_id = ?", [$course_id]);
    }
}
