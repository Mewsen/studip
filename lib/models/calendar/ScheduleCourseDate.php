<?php
/**
 * ScheduleCourseDate.php - Model class for regular course dates
 * in the schedule view.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Moritz Strohm <strohm@data-quest.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 * @since       6.0
 *
 * @property array $id alias for pk
 * @property string $user_id database column
 * @property string $course_id database column
 * @property string $metadate_id database_column
 * @property int $visible database column
 * @property int $mkdate database column
 * @property int $chdate database column
 * @property User $user belongs_to User
 * @property Course $course belongs_to Course
 * @property SeminarCycleDate $regular_date belongs_to SeminarCycleDate
 */
class ScheduleCourseDate extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'schedule_courses';
        $config['belongs_to']['user'] = [
            'class_name'  => User::class,
            'foreign_key' => 'user_id',
        ];
        $config['belongs_to']['course'] = [
            'class_name'  => Course::class,
            'foreign_key' => 'course_id',
        ];
        $config['belongs_to']['regular_date'] = [
            'class_name'  => SeminarCycleDate::class,
            'foreign_key' => 'metadate_id'
        ];
        parent::configure($config);
    }
}
