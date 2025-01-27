<?php

/**
 * @license GPL2 or any later version
 *
 * @property string $id alias column for tag_hash
 * @property string $studygroup_id database column
 * @property string $course_id database column
 * @property int $mkdate database column
 *
 */
class StudygroupCourse extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'studygroup_courses';
        $config['belongs_to']['course'] = [
            'class_name'        => Course::class,
            'foreign_key'       => 'course_id',
            'assoc_foreign_key' => 'seminar_id',
        ];
        $config['belongs_to']['studygroup'] = [
            'class_name'        => Course::class,
            'foreign_key'       => 'studygroup_id',
            'assoc_foreign_key' => 'seminar_id',
        ];
        parent::configure($config);
    }
}
