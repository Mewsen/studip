<?php
# Lifter010: TODO
/*
 * CommunityNavigation.php - navigation for community page
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

/**
 * Navigation for the community page used for user interaction.
 * It includes the contacts, study groups and ranking.
 */
class CommunityNavigation extends Navigation
{
    public function __construct()
    {
        parent::__construct(_('Community'));
    }

    public function initItem()
    {
        parent::initItem();
        $title = _('Community');

        $this->setImage(Icon::create('community', 'navigation', ["title" => $title]));
    }

    /**
     * Initialize the subnavigation of this item. This method
     * is called once before the first item is added or removed.
     */
    public function initSubNavigation()
    {
        global $perm;

        parent::initSubNavigation();

        // overview
        $navigation = new Navigation(_('Übersicht'), 'dispatch.php/community');
        $this->addSubNavigation('overview', $navigation);

        // groups
        $navigation = new Navigation(_('Spots'), 'dispatch.php/community/groups');
        $this->addSubNavigation('groups', $navigation);

        if (Config::get()->BLUBBER_GLOBAL_MESSENGER_ACTIVATE) {
            //Blubber messenger
            $navigation = new Navigation(_('Chat'), 'dispatch.php/blubber');
            $this->addSubNavigation('blubber', $navigation);
        }

        // contacts
        $navigation = new Navigation(_('Kontakte'), 'dispatch.php/contact');
        $this->addSubNavigation('contacts', $navigation);

        

        
    }
}
