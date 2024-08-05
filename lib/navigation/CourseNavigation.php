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
     * Add an array of navigation items to the subnavigation of this
     * object. The new items are inserted at the appropriate position
     * for this tool according to the order defined in tools_activated.
     *
     * @param int   $plugin_id   id of the module
     * @param array $navigations navigation items to add
     */
    public function addToolNavigation($plugin_id, array $navigations)
    {
        $found = null;
        $where = null;

        foreach ($this->range->tools as $tool) {
            if (
                $found
                && $tool->metadata['navigation']
                && $tool->metadata['navigation'] !== 'admin'
            ) {
                $where = $tool->metadata['navigation'];
                break;
            }

            if ($tool->plugin_id == $plugin_id) {
                $tool->metadata['navigation'] = key($navigations);
                $found = $tool;
            }
        }

        // always insert admin module in first position
        if (key($navigations) === 'admin') {
            $where = key($this->subnav);
        }

        foreach ($navigations as $key => $nav) {
            if (
                $this->range instanceof Institute
                || Seminar_Perm::get()->have_studip_perm($found->getVisibilityPermission(), $this->range->id)
            ) {
                if (isset($found->metadata['displayname'])) {
                    $nav->setTitle($found->getDisplayname());
                }

                $this->insertSubNavigation($key, $nav, $where);
            }
        }
    }
}
