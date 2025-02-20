<?php
/*
 * MultipleChoiceTask.php - Vips plugin for Stud.IP
 * Copyright (c) 2006-2009  Elmar Ludwig, Martin Schröder
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
class MultipleChoiceTask extends Exercise
{
    /**
     * Get the icon of this exercise type.
     */
    public static function getTypeIcon(string $role = Icon::DEFAULT_ROLE): Icon
    {
        return Icon::create('task-multiple-choice', $role);
    }

    /**
     * Get a description of this exercise type.
     */
    public static function getTypeDescription(): string
    {
        return _('Mehrfachauswahl aus einer Liste');
    }

    /**
     * Initialize this instance from the current request environment.
     */
    public function initFromRequest($request): void
    {
        parent::initFromRequest($request);

        foreach ($request['answer'] as $i => $answer) {
            $answer = self::purifyFlexibleInput($answer);

            if (trim($answer) != '') {
                $this->task['answers'][] = [
                    'text'  => trim($answer),
                    'score' => (int) $request['correct'][$i]
                ];
            }
        }
    }

    /**
     * Compute the default maximum points which can be reached in this
     * exercise, dependent on the number of answers.
     */
    public function itemCount(): int
    {
        return count($this->task['answers']);
    }

    /**
     * Return the default response when there is no existing solution.
     */
    public function defaultResponse(): array
    {
        return [];
    }

    /**
     * Shuffle the answer alternatives.
     *
     * @param $user_id string used for initialising the randomizer.
     */
    public function shuffleAnswers(string $user_id): void
    {
        srand(crc32($this->id . ':' . $user_id));

        $random_order = range(0, $this->itemCount() - 1);
        shuffle($random_order);

        $answer_temp = [];
        foreach ($random_order as $index) {
            $answer_temp[$index] = $this->task['answers'][$index];
        }
        $this->task['answers'] = $answer_temp;

        srand();
    }

    /**
     * Returns true if this exercise type is considered as multiple choice.
     * In this case, the evaluation mode set on the assignment is applied.
     */
    public function isMultipleChoice(): bool
    {
        return true;
    }

    /**
     * Evaluates a student's solution for the individual items in this
     * exercise. Returns an array of ('points' => float, 'safe' => boolean).
     *
     * @param mixed $solution The solution XML string as returned by responseFromRequest().
     */
    public function evaluateItems($solution): array
    {
        $result = [];

        $response = $solution->response;

        foreach ($this->task['answers'] as $i => $answer) {
            if (!isset($response[$i])) {
                $points = null;
            } else {
                $points = (int) $response[$i] == $answer['score'] ? 1 : 0;
            }

            $result[] = ['points' => $points, 'safe' => true];
        }

        return $result;
    }

    /**
     * Return the list of keywords used for text export. The first keyword
     * in the list must be the keyword for the exercise type.
     */
    public static function getTextKeywords(): array
    {
        return ['MC-Frage', '[+~]?Antwort'];
    }

    /**
     * Initialize this instance from the given text data array.
     */
    public function initText(array $exercise): void
    {
        parent::initText($exercise);

        foreach ($exercise as $tag) {
            if (key($tag) === '+Antwort') {
                $this->task['answers'][] = [
                    'text'  => Studip\Markup::purifyHtml(current($tag)),
                    'score' => 1
                ];
            } else if (key($tag) === 'Antwort') {
                $this->task['answers'][] = [
                    'text'  => Studip\Markup::purifyHtml(current($tag)),
                    'score' => 0
                ];
            }
        }
    }

    /**
     * Initialize this instance from the given SimpleXMLElement object.
     */
    public function initXML($exercise): void
    {
        parent::initXML($exercise);

        foreach ($exercise->items->item->answers->answer as $answer) {
            $this->task['answers'][] = [
                'text'  => Studip\Markup::purifyHtml(trim($answer)),
                'score' => (int) $answer['score']
            ];
        }
    }

    /**
     * Creates a template for editing a MultipleChoiceTask.
     */
    public function getEditTemplate(?VipsAssignment $assignment): Flexi\Template
    {
        if (!$this->task['answers']) {
            $this->task['answers'] = array_fill(0, 5, ['text' => '', 'score' => 0]);
        }

        return parent::getEditTemplate($assignment);
    }
}
