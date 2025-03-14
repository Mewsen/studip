<?php
/*
 * VipsTest.php - Vips test class for Stud.IP
 * Copyright (c) 2014  Elmar Ludwig
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

/**
 * @license GPL2 or any later version
 *
 * @property int $id database column
 * @property string $title database column
 * @property string $description database column
 * @property string $user_id database column
 * @property int $mkdate database column
 * @property int $chdate database column
 * @property string|null $options database column
 * @property SimpleORMapCollection<VipsAssignment> $assignments has_many VipsAssignment
 * @property SimpleORMapCollection<VipsExerciseRef> $exercise_refs has_many VipsExerciseRef
 * @property User $user belongs_to User
 * @property SimpleORMapCollection<Exercise> $exercises has_and_belongs_to_many Exercise
 */
class VipsTest extends SimpleORMap
{
    /**
     * Configure the database mapping.
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'etask_tests';

        // $config['serialized_fields']['options'] = 'JSONArrayObject';

        $config['has_and_belongs_to_many']['exercises'] = [
            'class_name'        => Exercise::class,
            'assoc_foreign_key' => 'id',
            'thru_table'        => 'etask_test_tasks',
            'thru_key'          => 'test_id',
            'thru_assoc_key'    => 'task_id',
            'order_by'          => 'ORDER BY position'
        ];

        $config['has_many']['assignments'] = [
            'class_name'        => VipsAssignment::class,
            'assoc_foreign_key' => 'test_id'
        ];
        $config['has_many']['exercise_refs'] = [
            'class_name'        => VipsExerciseRef::class,
            'assoc_foreign_key' => 'test_id',
            'on_delete'         => 'delete',
            'order_by'          => 'ORDER BY position'
        ];

        $config['belongs_to']['user'] = [
            'class_name'  => User::class,
            'foreign_key' => 'user_id'
        ];

        parent::configure($config);
    }

    public function addExercise(Exercise $exercise): VipsExerciseRef
    {
        $attributes = [
            'task_id' => $exercise->id,
            'test_id'     => $this->id,
            'position'    => count($this->exercise_refs) + 1,
            'points'      => $exercise->itemCount()
        ];

        $exercise_ref = VipsExerciseRef::create($attributes);

        $this->resetRelation('exercises');
        $this->resetRelation('exercise_refs');

        return $exercise_ref;
    }

    public function removeExercise(int $exercise_id): void
    {
        $db = DBManager::get();

        $exercise_ref = VipsExerciseRef::find([$this->id, $exercise_id]);
        $position     = $exercise_ref->position;

        if ($exercise_ref->delete()) {
            // renumber following exercises
            $sql = 'UPDATE etask_test_tasks SET position = position - 1 WHERE test_id = ? AND position > ?';
            $stmt = $db->prepare($sql);
            $stmt->execute([$this->id, $position]);
        }

        $this->resetRelation('exercises');
        $this->resetRelation('exercise_refs');
    }

    public function getExerciseRef(int $exercise_id): ?VipsExerciseRef
    {
        return $this->exercise_refs->findOneBy('task_id', $exercise_id);
    }

    /**
     * Return the maximum number of points a person can get on this test.
     *
     * @return integer  number of maximum points
     */
    public function getTotalPoints(): int
    {
        $points = 0;

        foreach ($this->exercise_refs as $exercise_ref) {
            $points += $exercise_ref->points;
        }

        return $points;
    }
}
