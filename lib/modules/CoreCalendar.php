<?php

/*
 *  Copyright (c) 2012  Rasmus Fuhse <fuhse@data-quest.de>
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 */

class CoreCalendar extends CorePlugin implements StudipModule
{
    /**
     * {@inheritdoc}
     */
    public function getIconNavigation(string $course_id, int $last_visit, string $user_id): ?Navigation
    {
        if (!Config::get()->CALENDAR_GROUP_ENABLE) {
            return null;
        }

        $navigation = new Navigation(_('Kalender'), URLHelper::getURL('dispatch.php/calendar/calendar/course/' . $course_id));
        $navigation->setImage(Icon::create('schedule', Icon::ROLE_CLICKABLE));
        return $navigation;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabNavigation(string $course_id): array
    {
        if (!Config::get()->CALENDAR_GROUP_ENABLE) {
            return [];
        }

        $navigation = new Navigation(_('Kalender'), 'dispatch.php/calendar/calendar/course/' . $course_id);
        $navigation->setImage(Icon::create('schedule', Icon::ROLE_INFO_ALT));
        $navigation->setActiveImage(Icon::create('schedule', Icon::ROLE_INFO));
        return ['calendar' => $navigation];
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata(): array
    {
        return [
            'summary' => _('Kalender'),
            'category' => _('Lehr- und Lernorganisation'),
            'icon' => Icon::create('schedule', Icon::ROLE_INFO),
            'displayname' => _('Kalender'),
        ];
    }

    public function isActivatableForContext(Range $context)
    {
        return Config::get()->CALENDAR_GROUP_ENABLE && $context->getRangeType() === 'course';
    }

    public function getInfoTemplate($course_id): ?Flexi_Template
    {
        return null;
    }
}
