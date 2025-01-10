<?php
/*
 * VipsGroup.php - Vips group class for Stud.IP
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
 * @property string $id alias column for statusgruppe_id
 * @property string $statusgruppe_id database column
 * @property string $name database column
 * @property string|null $description database column
 * @property string $range_id database column
 * @property int $position database column
 * @property int $size database column
 * @property int $selfassign database column
 * @property int $selfassign_start database column
 * @property int $selfassign_end database column
 * @property int $mkdate database column
 * @property int $chdate database column
 * @property int $calendar_group database column
 * @property string|null $name_w database column
 * @property string|null $name_m database column
 * @property SimpleORMapCollection|VipsGroupMember[] $members has_many VipsGroupMember
 * @property SimpleORMapCollection|VipsGroupMember[] $current_members has_many VipsGroupMember
 * @property Course $course belongs_to Course
 */
class VipsGroup extends SimpleORMap
{
    /**
     * Configure the database mapping.
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'statusgruppen';

        $config['has_many']['members'] = [
            'class_name'        => VipsGroupMember::class,
            'assoc_foreign_key' => 'group_id',
            'on_delete'         => 'delete'
        ];
        $config['has_many']['current_members'] = [
            'class_name'        => VipsGroupMember::class,
            'assoc_foreign_key' => 'group_id',
            'order_by'          => 'AND end IS NULL'
        ];

        $config['belongs_to']['course'] = [
            'class_name'  => Course::class,
            'foreign_key' => 'range_id'
        ];

        parent::configure($config);
    }

    /**
     * Get the group the user is currently assigned to in a course.
     * Returns null if there is no group assignment for this user.
     *
     * @param string $user_id   user id
     * @param string $course_id course id
     */
    public static function getUserGroup(string $user_id, string $course_id): ?VipsGroup
    {
        return self::findOneBySQL(
            'JOIN etask_group_members ON group_id = statusgruppe_id
             WHERE range_id  = ?
               AND user_id   = ?
               AND end IS NULL',
            [$course_id, $user_id]
        );
    }
}
