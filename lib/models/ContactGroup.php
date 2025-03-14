<?php
/**
 * The ContactGroup class represents a contact group of a user.
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
 * @property int $id The ID of the group.
 * @property string $name Name of the group.
 * @property string $owner_id The ID of the owner to whom the group belongs to.
 * @property int $mkdate The creation date of the group.
 * @property int $chdate The modification date of the group.
 * @property SimpleORMapCollection<ContactGroupItem> $items The items (users) that belong to the group.
 * @property User $owner The owner of the group.
 */
class ContactGroup extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'contact_groups';
        $config['belongs_to']['owner'] = [
            'class_name'  => User::class,
            'foreign_key' => 'owner_id'
        ];
        $config['has_many']['items'] = [
            'class_name'        => ContactGroupItem::class,
            'assoc_foreign_key' => 'group_id',
            'on_store'          => 'store',
            'on_delete'         => 'delete'
        ];
        parent::configure($config);
    }
}
