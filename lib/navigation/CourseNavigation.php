<?php
# Lifter010: TODO
/*
 * CourseNavigation.php - navigation for course / institute area
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Elmar Ludwig
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */

class CourseNavigation extends Navigation
{
    private $range;

    /**
     * Initialize a new Navigation instance.
     */
    public function __construct(Range $range)
    {
        if (!($range instanceof Course) && !($range instanceof Institute)) {
            throw new InvalidArgumentException('Invalid range type "' . get_class($range) . '" for course navigation');
        }

        $this->range = $range;

        // check if logged in
        if (User::findCurrent()) {
            $coursetext = _('Veranstaltungen');
            $courseinfo = _('Meine Veranstaltungen & Einrichtungen');
            $courselink = 'dispatch.php/my_courses';
        } else {
            $coursetext = _('Freie Veranstaltungen');
            $courseinfo = _('Freie Veranstaltungen');
            $courselink = 'dispatch.php/public_courses';
        }

        parent::__construct($coursetext, $courselink);

        if (User::findCurrent()) {
            $this->setImage(Icon::create('seminar', Icon::ROLE_NAVIGATION, ['title' => $courseinfo]));
        }
    }

    /**
     * Initialize the subnavigation of this item. This method
     * is called once before the first item is added or removed.
     */
    public function initSubNavigation()
    {
        parent::initSubNavigation();

        $tools = $this->range->tools->getArrayCopy();
        usort($tools, function ($a, $b) {
            return $a['position'] - $b['position'];
        });

        foreach ($tools as $tool) {
            if (
                !($this->range instanceof Institute)
                && !Seminar_Perm::get()->have_studip_perm($tool->getVisibilityPermission(), $this->range->id)
            ) {
                continue;
            }

            $studip_module = $tool->getStudipModule();
            if (!($studip_module instanceof StudipModule)) {
                continue;
            }

            $tool_nav = $studip_module->getTabNavigation($this->range->id) ?: [];

            foreach ($tool_nav as $nav_name => $navigation) {
                if (!$nav_name || !$navigation instanceof Navigation) {
                    continue;
                }

                if ($tool->metadata['displayname']) {
                    $navigation->setTitle($tool->getDisplayname());
                }
                $this->addSubNavigation($nav_name, $navigation);
            }
        }
    }
}
