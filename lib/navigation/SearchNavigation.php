<?php
# Lifter010: TODO
/*
 * SearchNavigation.php - navigation for search page
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Elmar Ludwig
 * @author      Michael Riehemann <michael.riehemann@uni-oldenburg.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 * @since       2.0
 */

/**
 * This navigation includes all search pages depending on the
 * activated modules.
 */
class SearchNavigation extends Navigation
{
    /**
     * Initialize a new Navigation instance.
     */
    public function __construct()
    {
        parent::__construct(_('Suche'));

        $this->setImage(Icon::create('search', 'navigation', ['title' => _('Suche')]));
    }

    /**
     * Returns a new navigation object for the sidebar according to the name
     * of the option.
     * The option name is the key of an entry in the array with the navigation
     * options.
     *
     * The navigation options are configured in the global configuration as an
     * array. For further details see documentation of entry
     * COURSE_SEARCH_NAVIGATION_OPTIONS in global configuration.
     *
     * This is an example with all possible options.
     * Note that the "target" attribute has no meaning anymore and is only there
     * for backwards compatibility with existing configurations. The target is
     * now hardcoded to "sidebar".
     *
     * {
     *     // "courses", "semtree" and "rangetree" are the "old" search options.
     *     // The link text is fixed.
     *     "courses":{
     *         "visible":true,
     *         // The target indicates where the link to this search option is
     *         // placed. Possible values are "sidebar" for a link in the sidebar
     *         // or "courses" to show a link (maybe with picture) below the
     *         // "course search".
     *         "target":"sidebar"
     *     },
     *     "semtree":{
     *         "visible":true,
     *         "target":"sidebar"
     *     },
     *     "rangetree":{
     *         "visible":false,
     *         "target":"sidebar"
     *     },
     *     // New option to acivate the search for modules and the systematic
     *     // search in studycourses, field of study and degrees.
     *     "module":{
     *         "visible":true,
     *         "target":"sidebar"
     *     },
     *     // This option shows a direct link in the sidebar to an entry (level)
     *     // in the range tree. The link text is the name of the level.
     *     "fb3_hist":{
     *         "visible":true,
     *         "target":"sidebar",
     *         "range_tree_id":"d1a07cf0c8057c664279214cc070b580"
     *     },
     *     // The same for an entry in the sem tree.
     *     "grundstudium":{
     *         "visible":true,
     *         "target":"sidebar",
     *         "sem_tree_id":"e1a07cf0c8057c664279214cc070b580"
     *     },
     *     // This shows a link in the sidebar to the course search. The text is
     *     // availlable in two languages.
     *     "vvz":{
     *         "visible":true,
     *         "target":"sidebar",
     *         "url":"dispatch.php/search/courses?level=f&option=vav",
     *         "title":{
     *             "de_DE":"Veranstaltungsverzeichnis",
     *             "en_GB":"Course Catalogue"
     *         }
     *     },
     *     // This option uses an url with search option and shows a link in the
     *     // sidebar to an entry in the range tree with all courses.
     *     "test":{
     *         "visible":true,
     *         "target":"sidebar",
     *         "url":"dispatch.php/search/courses?start_item_id=d1a07cf0c8057c664279214cc070b580&cmd=show_sem_range_tree&item_id=d1a07cf0c8057c664279214cc070b580_withkids&level=ev",
     *         "title":{
     *             "de_DE":"Historisches Institut",
     *             "en_GB":"Historical Institute"
     *         }
     *     },
     *     // This option shows a link to the sem tree with picture below the
     *     // course search (target: courses).
     *     // This is the behaviour of Stud.IP < 4.2.
     *     "csemtree":{
     *         "visible":true,
     *         "target":"courses",
     *         "url":"dispatch.php/search/courses?level=vv",
     *         "img":{
     *             "filename":"directory-search.png",
     *             "attributes":{
     *                 "size":"260@100"
     *             }
     *         },
     *         "title":{
     *             "de_DE":"Suche im Vorlesungsverzeichnis",
     *             "en_GB":"Search course directory"
     *         }
     *     },
     *     // This option shows a link to the range tree with picture below the
     *     // course search (target: courses).
     *     // This is the behaviour of Stud.IP < 4.2.
     *     "crangetree":{
     *         "visible":true,
     *         "target":"courses",
     *         "url":"dispatch.php/search/courses?level=ev",
     *         "img":{
     *             "filename":"institute-search.png",
     *             "attributes":{
     *                 "size":"260@100"
     *             }
     *         },
     *         "title":{
     *             "de_DE":"Suche in Einrichtungen",
     *             "en_GB":"Search institutes"
     *         }
     *     }
     * }
     *
     *
     * @param string $option_name
     * @return Navigation|null
     */
    protected function getSearchOptionNavigation(?string $option_name = null): ?Navigation
    {
        // return first visible search option
        if ($option_name === null) {
            $options = Config::get()->COURSE_SEARCH_NAVIGATION_OPTIONS;
            foreach ($options as $name => $option) {
                if ($option['visible'] && $option['target'] === 'sidebar') {
                    return $this->getSearchOptionNavigation($name);
                }
            }
            return null;
        }

        $installed_languages = array_keys(Config::get()->INSTALLED_LANGUAGES);
        $language = $_SESSION['_language'] ?? reset($installed_languages);
        $option = Config::get()->COURSE_SEARCH_NAVIGATION_OPTIONS[$option_name];
        if (!$option['visible'] || $option['target'] !== 'sidebar') {
            return null;
        }
        if (empty($option['url'])) {
            return match ($option_name) {
                'courses',
                'semtree' =>
                    new Navigation(
                        _('Vorlesungsverzeichnis'),
                        URLHelper::getURL('dispatch.php/search/courses', ['type' => 'semtree'], true)
                    ),
                'rangetree' =>
                    new Navigation(
                        _('Einrichtungsverzeichnis'),
                        URLHelper::getURL('dispatch.php/search/courses', ['type' => 'rangetree'], true)
                    ),
                'module' =>
                    new MVVSearchNavigation(
                        _('Modulverzeichnis'),
                        URLHelper::getURL('dispatch.php/search/module'),
                        null,
                        true
                    )
            };
        } else {
            return new Navigation($option['title'][$language],
                URLHelper::getURL($option['url'], ['option' => $option_name], true));
        }
    }

    /**
     * Initialize the subnavigation of this item. This method
     * is called once before the first item is added or removed.
     */
    public function initSubNavigation()
    {
        parent::initSubNavigation();
        if($GLOBALS['user']->id != 'nobody'){
            // global search
            $navigation = new Navigation(_('Globale Suche'), 'dispatch.php/search/globalsearch');
            $this->addSubNavigation('globalsearch', $navigation);
        }

        // browse courses
        // get first search option
        if (($GLOBALS['user']->id == 'nobody' && Config::get()->COURSE_SEARCH_IS_VISIBLE_NOBODY) || $GLOBALS['user']->id != 'nobody') {
            $navigation_option = $this->getSearchOptionNavigation();

            if ($navigation_option) {
                $navigation = new Navigation(
                    _('Veranstaltungsverzeichnis'),
                    $navigation_option->getURL()
                );
                foreach (array_keys(Config::get()->COURSE_SEARCH_NAVIGATION_OPTIONS) as $name) {
                    $navigation_option = $this->getSearchOptionNavigation($name);
                    if ($navigation_option) {
                        $navigation->addSubNavigation($name, $navigation_option);
                    }
                }
                    $this->addSubNavigation('courses', $navigation);
            }
        }


        if ($GLOBALS['user']->id != 'nobody') {
            // search archive
            if (Config::get()->ENABLE_ARCHIVE_SEARCH) {
                $navigation = new Navigation(_('Archiv'), 'dispatch.php/search/archive');
                $this->addSubNavigation('archive', $navigation);
            }

            // browse resources
            if (Config::get()->RESOURCES_ENABLE) {
                $navigation = new Navigation(
                    _('Räume'),
                    'dispatch.php/resources/search/rooms'
                );
                $this->addSubNavigation('rooms', $navigation);
            }
        }
    }
}
