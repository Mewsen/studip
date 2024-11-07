<?php
/*
 *  Copyright (c) 2012  Rasmus Fuhse <fuhse@data-quest.de>
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 */

class CorePersonal extends CorePlugin implements StudipModule
{
    public function getIconNavigation(string $course_id, int $last_visit, string $user_id): ?Navigation
    {
        return null;
    }

    public function getTabNavigation(string $course_id): array
    {
        if ($GLOBALS['user']->id != 'nobody') {
            $navigation = new Navigation(_('Personal'));
            $navigation->setImage(Icon::create('persons', Icon::ROLE_INFO_ALT));
            $navigation->setActiveImage(Icon::create('persons', Icon::ROLE_INFO));
            $navigation->addSubNavigation('view', new Navigation(_('MitarbeiterInnen'), 'dispatch.php/institute/members'));

            if ($GLOBALS['perm']->have_studip_perm('tutor', $course_id) && $GLOBALS['perm']->have_perm('admin')) {
                $navigation->addSubNavigation('edit_groups', new Navigation(_('Funktionen / Gruppen verwalten'), 'dispatch.php/admin/statusgroups'));
            }

            return ['faculty' => $navigation];
        } else {
            return [];
        }
    }

    public function isActivatableForContext(Range $context)
    {
        return $context->getRangeType() === 'institute';
    }

    /**
     * @see StudipModule::getMetadata()
     */
    public function getMetadata(): array
    {
        return [
            'summary'          => _('Liste aller MitarbeiterInnen'),
            'description'      => '',
            'displayname'      => _('MitarbeiterInnen'),
            'category'         => _('Sonstiges'),
            'icon'             => Icon::create('persons', Icon::ROLE_INFO),
            'icon_clickable'   => Icon::create('persons', Icon::ROLE_CLICKABLE)
        ];
    }

    public function getInfoTemplate($course_id): ?Flexi_Template
    {
        // TODO: Implement getInfoTemplate() method.
        return null;
    }
}
