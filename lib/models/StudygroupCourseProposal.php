<?php

/**
 * @license GPL2 or any later version
 *
 * @property int $id database column
 * @property string $studygroup_id database column
 * @property string $course_id database column
 * @property string $proposed_from database column 'course' or 'studygroup'
 * @property string|null $user_id database column
 * @property int|null $mkdate database column
 * @property Course $course belongs_to Course
 * @property Course $studygroup belongs_to Course
 * @property User|null $user belongs_to User
 */
class StudygroupCourseProposal extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'studygroup_courses_proposals';
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
        $config['belongs_to']['user'] = [
            'class_name'        => User::class,
            'foreign_key'       => 'user_id'
        ];
        parent::configure($config);
    }

}
