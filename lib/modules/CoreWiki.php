<?php

/*
 *  Copyright (c) 2012  Rasmus Fuhse <fuhse@data-quest.de>
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 */

class CoreWiki extends CorePlugin implements StudipModuleExtended
{
    use IconNavigationTrait;

    public function getManyIconNavigation(array $course_ids, ?string $user_id = null): array
    {
        if (!Config::get()->WIKI_ENABLE) {
            return [];
        }
        $perm = $GLOBALS['perm']->get_perm($user_id);
        if (in_array($perm, ['admin', 'root'])) {
            $perm = 'dozent';
        }

        $query = "SELECT wiki_pages.range_id,
                    COUNT(page_id) AS count,
                    COUNT(IF((wiki_pages.chdate > IFNULL(ouv.visitdate, :threshold) AND wiki_pages.user_id != :user_id), page_id, NULL)) AS neue
            FROM wiki_pages
            LEFT JOIN statusgruppe_user ON (statusgruppe_user.statusgruppe_id = wiki_pages.read_permission)
            LEFT JOIN object_user_visits AS ouv
              ON ouv.object_id = wiki_pages.range_id
                AND ouv.user_id = :user_id
                AND ouv.plugin_id = :plugin_id
            WHERE wiki_pages.range_id IN (:range_ids)
              AND (
                wiki_pages.read_permission = 'all'
                OR statusgruppe_user.user_id = :user_id
                OR wiki_pages.read_permission = :perm
                OR (wiki_pages.read_permission = 'tutor' AND :perm = 'dozent')
              )
            GROUP BY wiki_pages.range_id;";
        $results = DBManager::get()->fetchAll($query, [
            ':range_ids' => $course_ids,
            ':user_id' => $user_id,
            ':perm' => $perm,
            ':plugin_id' => $this->getPluginId(),
            ':threshold' => object_get_visit_threshold(),
        ]);

        $navs = array_fill_keys($course_ids, null);
        foreach ($results as $result) {
            $nav = new Navigation(_('Wiki'));
            $params = [
                'title' => sprintf(
                    ngettext(
                        '%d Wiki-Seite',
                        '%d Wiki-Seiten',
                        $result['count']
                    ),
                    $result['count']
                )
            ];
            if ($result['neue']) {
                $nav->setURL('dispatch.php/course/wiki/newpages');
                $nav->setImage(Icon::create('wiki', Icon::ROLE_ATTENTION));
                $params['title'] .= ', ' . sprintf(
                        ngettext(
                            '%d Änderung',
                            '%d Änderungen',
                            $result['neue']
                        ),
                        $result['neue']
                    );
                $nav->setBadgeNumber($result['neue']);
            } else {
                $nav->setURL('dispatch.php/course/wiki/page');
                $nav->setImage(Icon::create('wiki'));

            }
            $nav->setLinkAttributes($params);
            $navs[$result['range_id']] = $nav;
        }
        return $navs;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabNavigation($range_id)
    {
        if (!Config::get()->WIKI_ENABLE) {
            return null;
        }

        $navigation = new Navigation(_('Wiki'));
        $navigation->setImage(Icon::create('wiki', Icon::ROLE_INFO_ALT));
        $navigation->setActiveImage(Icon::create('wiki', Icon::ROLE_INFO));

        $id = RangeConfig::get($range_id)->WIKI_STARTPAGE_ID;
        $startpage = $id ? WikiPage::find($id) : false;

        $title = $startpage ? htmlReady($startpage->name) : _('Wiki-Startseite');
        $navigation->addSubNavigation('start', new Navigation($title, 'dispatch.php/course/wiki/page'));
        if (WikiPage::countBySQL('`range_id` = ?', [$range_id]) > 0) {
            if ($GLOBALS['perm']->have_studip_perm('user', $range_id)) {
                $navigation->addSubNavigation('listnew', new Navigation(_('Neue Seiten'), 'dispatch.php/course/wiki/newpages'));
            }
            $navigation->addSubNavigation('allpages', new Navigation(_('Alle Seiten'), 'dispatch.php/course/wiki/allpages'));
        }
        return ['wiki' => $navigation];
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        return [
            'summary' => _('Gemeinsames Erstellen und Bearbeiten von Texten'),
            'description' => _('Im Wiki können '.
                'verschiedene Autor/-innen gemeinsam Texte, Konzepte und andere '.
                'schriftliche Arbeiten erstellen und gestalten, dies '.
                'allerdings nicht gleichzeitig. Texte können individuell '.
                'bearbeitet und die Änderungen gespeichert werden. Das '.
                'Besondere im Wiki ist, dass Studierende und Lehrende '.
                'annähernd die gleichen Rechte (schreiben, lesen, ändern, '.
                'löschen) haben, was sich nicht einschränken lässt. Das '.
                'System erstellt eine Versionshistorie, mit der Änderungen '.
                'nachvollziehbar werden. Einzelne Versionen können zudem '.
                'auch gelöscht werden (nur Lehrende). Ein Export als '.
                'pdf-Datei ist integriert.'),

            'displayname' => _('Wiki-Web'),
            'keywords' => _('Individuelle Bearbeitung von Texten;
                            Versionshistorie;
                            Druckansicht und PDF-Export;
                            Löschfunktion für die aktuellste Seiten-Version;
                            Keine gleichzeitige Bearbeitung desselben Textes möglich, nur nacheinander'),
            'descriptionshort' => _('Gemeinsames asynchrones Erstellen und Bearbeiten von Texten'),
            'descriptionlong' => _('Im Wiki können verschiedene Autor/-innen gemeinsam Texte, '.
                                    'Konzepte und andere schriftliche Arbeiten erstellen und gestalten. Dies '.
                                    'allerdings nicht gleichzeitig. Texte können individuell bearbeitet und '.
                                    'gespeichert werden. Das Besondere im Wiki ist, dass Studierende und Lehrende '.
                                    'annähernd die gleichen Rechte (schreiben, lesen, ändern, löschen) haben, was '.
                                    'gegenseitiges Vertrauen voraussetzt. Das System erstellt eine Versionshistorie, '.
                                    'mit der Änderungen nachvollziehbar werden. Einzelne Versionen können zudem auch '.
                                    'gelöscht werden (nur Lehrende). Eine Druckansicht und eine Exportmöglichkeit als '.
                                    'PDF-Datei ist integriert.'),
            'category' => _('Kommunikation und Zusammenarbeit'),
            'icon' => Icon::create('wiki', Icon::ROLE_INFO),
            'icon_clickable' => Icon::create('wiki'),
            'screenshots' => [
                'path' => 'assets/images/plus/screenshots/Wiki-Web',
                'pictures' => [
                    0 => [ 'source' => 'Wiki_Seite.jpg', 'title' => _('Wiki Seite')],
                    1 => [ 'source' => 'Wiki_Seite_bearbeiten.jpg', 'title' => _('Wiki Seite bearbeiten')]
                ]
            ]
        ];
    }

    public function getInfoTemplate($course_id)
    {
        return null;
    }


    /**
     * Generates a TOCItem tree containing all pages in the currently opened wiki
     * for use in table of contents/breadcrumbs.
     * To prevent cyclic data references, the TOCItems in the tree do not contain
     * references to their parent pages.
     * This allows the resultant TOCItem to be serialized via json_decode for
     * use in Vue.
     *
     * @param $activePage WikiPage The page that the user has currently navigated to.
     * @return TOCItem A TOCItem for the root of the wiki and all of its descendants.
     */
    public static function getTOC(WikiPage $activePage): TOCItem
    {
        $rootId = RangeConfig::get(Context::getId())->WIKI_STARTPAGE_ID;
        $rootPage = WikiPage::find($rootId) ?: $activePage;

        $rootToc = self::getTOCRecursive($rootPage, $activePage->page_id);
        $rootToc->setTitle(htmlReady($rootPage->name));
        $rootToc->setIcon(Icon::create('wiki'));
        return $rootToc;
    }

    /**
     * Using a recursive depth-first traversal of the wiki page hierarchy,
     * create a TOCItem tree starting at the given $page.
     *
     * @param WikiPage $page The currently visited page in the traversal.
     * @param int|null $active_page_id The id of the page that the user has navigated to.
     * @return TOCItem A TOCItem for the given $page and all of its descendants
     */
    private static function getTOCRecursive(WikiPage $page, int|null $active_page_id): TOCItem
    {
        $toc = new TOCItem($page->name);
        $toc->setURL($page->isNew() ? URLHelper::getURL('dispatch.php/course/wiki/page') : URLHelper::getURL('dispatch.php/course/wiki/page/' . $page->id));
        $toc->setActive($page->page_id == $active_page_id);
        foreach ($page->children as $child) {
            $childToc = self::getTOCRecursive($child, $active_page_id);
            $toc->children[] = $childToc;
        }
        return $toc;
    }

    public function isActivatableForContext(Range $context)
    {
        return (bool) Config::get()->getValue('WIKI_ENABLE');
    }
}
