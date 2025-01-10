<?php
/*
 * VipsGroupMember.php - Vips group member class for Stud.IP
 * Copyright (c) 2016  Elmar Ludwig
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

/**
 * @license GPL2 or any later version
 *
 * @property array $id alias for pk
 * @property string $group_id database column
 * @property string $user_id database column
 * @property int $start database column
 * @property int|null $end database column
 * @property VipsGroup $group belongs_to VipsGroup
 * @property User $user belongs_to User
 * @property mixed $vorname additional field
 * @property mixed $nachname additional field
 * @property mixed $username additional field
 */
class VipsGroupMember extends SimpleORMap
{
    /**
     * Configure the database mapping.
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'etask_group_members';

        $config['additional_fields']['vorname'] = ['user', 'vorname'];
        $config['additional_fields']['nachname'] = ['user', 'nachname'];
        $config['additional_fields']['username'] = ['user', 'username'];

        $config['belongs_to']['group'] = [
            'class_name'  => VipsGroup::class,
            'foreign_key' => 'group_id'
        ];
        $config['belongs_to']['user'] = [
            'class_name'  => User::class,
            'foreign_key' => 'user_id'
        ];

        parent::configure($config);
    }
}
