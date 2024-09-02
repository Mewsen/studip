<?php
/*
 * This class displays a seminar-schedule for
 * users on a seminar-based view and for admins on an institute based view
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Till Glöggler <tgloeggl@uos.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */

class ScheduleWidget extends CorePlugin implements PortalPlugin
{
    /**
     * Returns the name of the plugin/widget.
     *
     * @return String containing the name
     */
    public function getPluginName()
    {
        return _('Mein Stundenplan');
    }

    public function getMetadata()
    {
        return [
            'description' => _('Mit diesem Widget haben Sie eine Übersicht Ihres aktuellen Stundenplans.')
        ];
    }

    /**
     * Return the template for the widget.
     *
     * @return Flexi\PhpTemplate The template containing the widget contents
     */
    public function getPortalTemplate()
    {
        $template = $GLOBALS['template_factory']->open('start/schedule_widget');
        $template->fullcalendar = \Studip\Calendar\Helper::getScheduleFullcalendar()->render();
        return $template;
    }
}
