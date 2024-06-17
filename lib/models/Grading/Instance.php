<?php

namespace Grading;

class Instance extends \SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'grading_instances';

        $config['belongs_to']['user'] = [
            'class_name' => \User::class,
            'foreign_key' => 'user_id',
        ];
        $config['belongs_to']['definition'] = [
            'class_name' => Definition::class,
            'foreign_key' => 'definition_id',
        ];

        parent::configure($config);
    }

    public static function findByCourse(\Course $course)
    {
        $definitionIds = Definition::findAndMapBySQL(
            function ($def) {
                return $def->id;
            },
            'course_id = ?',
            [$course->id]
        );

        if (!count($definitionIds)) {
            return [];
        }

        return self::findBySql('definition_id IN (?)', [$definitionIds]);
    }

    public static function findByCourseAndUser(\Course $course, \User $user)
    {
        $definitionIds = Definition::findAndMapBySQL(
            function ($def) {
                return $def->id;
            },
            'course_id = ?',
            [$course->id]
        );

        if (!count($definitionIds)) {
            return [];
        }

        return self::findBySql('definition_id IN (?) AND user_id = ?', [$definitionIds, $user->id]);
    }

    /**
     * setter for the rawgrade column. The database type is decimal(6,5) UNSIGNED, therefore
     * the setter mimics the database behaviour to get valid results from ::isFieldDirty()
     *
     * @param mixed $grade
     * @return string
     */
    public function setRawgrade($grade = 0): string
    {
        if ($grade < 0) {
            $grade = 0;
        }
        if ($grade >= 10) {
            $grade = 9.99999;
        }
        return $this->content['rawgrade'] = number_format($grade, 5, '.', '');
    }
}
