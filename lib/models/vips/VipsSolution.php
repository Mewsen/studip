<?php
/*
 * VipsSolution.php - Vips solution class for Stud.IP
 * Copyright (c) 2014  Elmar Ludwig
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

/**
 *
 * @property int $id database column
 * @property int $assignment_id database column
 * @property int $task_id database column
 * @property string $user_id database column
 * @property JSONArrayObject $response database column
 * @property string|null $student_comment database column
 * @property string $ip_address database column
 * @property int|null $state database column
 * @property float|null $points database column
 * @property string|null $feedback database column
 * @property string|null $commented_solution database column
 * @property string|null $grader_id database column
 * @property int $mkdate database column
 * @property int $chdate database column
 * @property JSONArrayObject $options database column
 * @property Exercise $exercise belongs_to Exercise
 * @property VipsAssignment $assignment belongs_to VipsAssignment
 * @property User $user belongs_to User
 * @property Folder $folder has_one Folder
 * @property Folder $feedback_folder has_one Folder
 */
class VipsSolution extends SimpleORMap
{
    /**
     * Configure the database mapping.
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'etask_responses';

        $config['serialized_fields']['response'] = JSONArrayObject::class;
        $config['serialized_fields']['options'] = JSONArrayObject::class;

        $config['registered_callbacks']['after_store'][] = 'after_store';

        $config['has_one']['folder'] = [
            'class_name'        => Folder::class,
            'assoc_foreign_key' => 'range_id',
            'assoc_func'        => 'findByRangeIdAndFolderType',
            'foreign_key'       => fn($record) => [$record->getId(), 'ResponseFolder'],
            'on_delete'         => 'delete'
        ];
        $config['has_one']['feedback_folder'] = [
            'class_name'        => Folder::class,
            'assoc_foreign_key' => 'range_id',
            'assoc_func'        => 'findByRangeIdAndFolderType',
            'foreign_key'       => fn($record) => [$record->getId(), 'FeedbackFolder'],
            'on_delete'         => 'delete'
        ];

        $config['belongs_to']['exercise'] = [
            'class_name'  => Exercise::class,
            'foreign_key' => 'task_id'
        ];
        $config['belongs_to']['assignment'] = [
            'class_name'  => VipsAssignment::class,
            'foreign_key' => 'assignment_id'
        ];
        $config['belongs_to']['user'] = [
            'class_name'  => User::class,
            'foreign_key' => 'user_id'
        ];

        parent::configure($config);
    }

    /**
     * Update the gradebook entry.
     */
    public function after_store(): void
    {
        $this->assignment->updateGradebookEntries($this->user_id);
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
     * Get array of submitted answers for this solution (PHP array).
     */
    public function getResponse(): array
    {
        return $this->content['response']->getArrayCopy();
    }

    /**
     * Check if this solution is archived.
     */
    public function isArchived(): bool
    {
        $solution = VipsSolution::findOneBySql(
            'task_id = ? AND assignment_id = ? AND user_id = ? ORDER BY id DESC',
            [$this->task_id, $this->assignment_id, $this->user_id]
        );

        return $solution && $this->id != $solution->id;
    }

    /**
     * Check if this solution is empty (default response and no files).
     */
    public function isEmpty(): bool
    {
        return $this->response == $this->exercise->defaultResponse()
            && $this->student_comment == ''
            && (!$this->folder || count($this->folder->file_refs) === 0);
    }

    /**
     * Check if this solution has been submitted (is not a dummy solution).
     */
    public function isSubmitted(): bool
    {
        return $this->id && !$this->mkdate;
    }

    /**
     * Check if this solution has any corrector feedback (text or files).
     */
    public function hasFeedback()
    {
        return $this->feedback
            || ($this->feedback_folder && count($this->feedback_folder->file_refs) > 0);
    }

    /**
     * Return the total number of solutions (including archived ones)
     * submitted by the same user for this exercise.
     */
    public function countTries(): int
    {
        if ($this->isNew()) {
            return 0;
        }

        return VipsSolution::countBySql(
            'task_id = ? AND assignment_id = ? AND user_id = ?',
            [$this->task_id, $this->assignment_id, $this->user_id]
        );
    }
}
