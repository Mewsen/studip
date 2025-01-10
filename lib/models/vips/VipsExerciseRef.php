<?php
/*
 * VipsExerciseRef.php - Vips exercise reference class for Stud.IP
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
 * @property int $test_id database column
 * @property int $task_id database column
 * @property int $position database column
 * @property int $part database column
 * @property float|null $points database column
 * @property string $options database column
 * @property int|null $mkdate database column
 * @property int|null $chdate database column
 * @property Exercise $exercise belongs_to Exercise
 * @property VipsTest $test belongs_to VipsTest
 */
class VipsExerciseRef extends SimpleORMap
{
    /**
     * Configure the database mapping.
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'etask_test_tasks';

        $config['belongs_to']['exercise'] = [
            'class_name'  => Exercise::class,
            'foreign_key' => 'task_id'
        ];
        $config['belongs_to']['test'] = [
            'class_name'  => VipsTest::class,
            'foreign_key' => 'test_id'
        ];

        parent::configure($config);
    }

    /**
     * Set value for the "exercise" relation (to avoid SORM errors).
     */
    public function setExercise(Exercise $exercise): void
    {
        $this->task_id = $exercise->id;
        $this->relations['exercise'] = $exercise;
    }

    /**
     * Delete entry from the database.
     */
    public function delete()
    {
        $ref_count = self::countBySql('task_id = ?', [$this->task_id]);

        if ($ref_count == 1) {
            $this->exercise->delete();
        }

        return parent::delete();
    }

    /**
     * Copy the referenced exercise into the given test at the specified
     * position (or at the end). Returns the new exercise reference.
     *
     * @param string $test_id   test id
     * @param int $position     exercise position (optional)
     */
    public function copyIntoTest(string $test_id, ?int $position = null): VipsExerciseRef
    {
        $db = DBManager::get();

        if ($position === null) {
            $stmt = $db->prepare('SELECT MAX(position) FROM etask_test_tasks WHERE test_id = ?');
            $stmt->execute([$test_id]);
            $position = $stmt->fetchColumn() + 1;
        }

        $new_exercise = Exercise::create([
            'type'        => $this->exercise->type,
            'title'       => $this->exercise->title,
            'description' => $this->exercise->description,
            'task'   => $this->exercise->task,
            'options'     => $this->exercise->options,
            'user_id'     => $GLOBALS['user']->id
        ]);

        if ($this->exercise->folder) {
            $folder = Folder::findTopFolder($new_exercise->id, 'ExerciseFolder', 'task');

            foreach ($this->exercise->folder->file_refs as $file_ref) {
                FileManager::copyFile($file_ref->getFileType(), $folder->getTypedFolder(), User::findCurrent());
            }
        }

        return VipsExerciseRef::create([
            'task_id' => $new_exercise->id,
            'test_id'     => $test_id,
            'points'      => $this->points,
            'position'    => $position
        ]);
    }

    /**
     * Move the referenced exercise into the given test (at the end).
     *
     * @param string $test_id   test id
     */
    public function moveIntoTest(string $test_id): void
    {
        $db = DBManager::get();
        $old_test_id = $this->test_id;
        $old_position = $this->position;

        if ($old_test_id != $test_id) {
            $stmt = $db->prepare('SELECT MAX(position) FROM etask_test_tasks WHERE test_id = ?');
            $stmt->execute([$test_id]);
            $this->position = $stmt->fetchColumn() + 1;
            $this->test_id = $test_id;
            $this->store();

            // renumber following exercises
            $sql = 'UPDATE etask_test_tasks SET position = position - 1 WHERE test_id = ? AND position > ?';
            $stmt = $db->prepare($sql);
            $stmt->execute([$old_test_id, $old_position]);
        }
    }
}
