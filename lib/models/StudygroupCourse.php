<?php

/**
 * @license GPL2 or any later version
 *
 * @property int $id database column
 * @property string $studygroup_id database column
 * @property string|null $course_id database column
 * @property int|null $mkdate database column
 * @property Course|null $course belongs_to Course
 * @property Course $studygroup belongs_to Course
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
