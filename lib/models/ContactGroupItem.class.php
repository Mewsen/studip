<?php
/**
 * The ContactGroupItem class represents an item in a contact group.
 *
 * This file is part of Stud.IP
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Moritz Strohm <strohm@data-quest.de>
 * @copyright   2023
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 * @package     resources
 * @since       5.5
 *
 * @property array $id alias for pk
 * @property int $group_id database column
 * @property string $user_id database column
 * @property int $mkdate database column
 * @property int $chdate database column
 * @property ContactGroup $contact_group belongs_to ContactGroup
 * @property User $user belongs_to User
 */
class ContactGroupItem extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'contact_group_items';
        $config['belongs_to']['contact_group'] = [
            'class_name'  => ContactGroup::class,
            'foreign_key' => 'group_id'
        ];
        $config['belongs_to']['user'] = [
            'class_name'  => User::class,
            'foreign_key' => 'user_id'
        ];
        parent::configure($config);
    }

    /**
     * Finds and returns all group items for a contact.
     *
     * @param Contact $contact The contact for which to find all contact group items.
     * @return ContactGroupItem[] All memberships of the contact.
     */
    public static function findByContact(Contact $contact): array
    {
        return self::findBySQL(
            'JOIN `contact_groups`
              ON (`contact_group_items`.`group_id` = `contact_groups`.`id`)
             WHERE `contact_groups`.`owner_id` = :owner_id
               AND `contact_group_items`.`user_id` = :user_id',
            [
                'owner_id' => $contact->owner_id,
                'user_id' => $contact->user_id
            ]
        );
    }
}
