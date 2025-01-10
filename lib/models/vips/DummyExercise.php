<?php
/*
 * DummyExercise.php - Vips plugin for Stud.IP
 * Copyright (c) 2021  Elmar Ludwig
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
 * @property string $type database column
 * @property string $title database column
 * @property string $description database column
 * @property string $task database column
 * @property string $user_id database column
 * @property int $mkdate database column
 * @property int $chdate database column
 * @property JSONArrayObject $options database column
 * @property SimpleORMapCollection|VipsExerciseRef[] $exercise_refs has_many VipsExerciseRef
 * @property SimpleORMapCollection|VipsSolution[] $solutions has_many VipsSolution
 * @property User $user belongs_to User
 * @property Folder $folder has_one Folder
 * @property SimpleORMapCollection|VipsTest[] $tests has_and_belongs_to_many VipsTest
 */
class DummyExercise extends Exercise
{
    /**
     * Get the name of this exercise type.
     */
    public function getTypeName(): string
    {
        return _('Unbekannter Aufgabentyp');
    }

    /**
     * Evaluates a student's solution for the individual items in this
     * exercise. Returns an array of ('points' => float, 'safe' => boolean).
     */
    public function evaluateItems($solution): array
    {
        return [];
    }

    /**
     * Compute the default maximum points which can be reached in this
     * exercise, dependent on the number of answers (defaults to 1).
     */
    public function itemCount(): int
    {
        return 0;
    }

    /**
     * Create a template for editing an exercise.
     */
    public function getEditTemplate(?VipsAssignment $assignment): Flexi\Template
    {
        $template = $GLOBALS['template_factory']->open('shared/string');
        $template->content = '';

        return $template;
    }

    /**
     * Create a template for viewing an exercise.
     */
    public function getViewTemplate(
        string $view,
        ?VipsSolution $solution,
        VipsAssignment $assignment,
        ?string $user_id
    ): Flexi\Template {
        $template = $GLOBALS['template_factory']->open('shared/string');
        $template->content = '';

        return $template;
    }
}
