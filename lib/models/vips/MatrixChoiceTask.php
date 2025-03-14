<?php
/*
 * MatrixChoiceTask.php - Vips plugin for Stud.IP
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
 * @property SimpleORMapCollection<VipsExerciseRef> $exercise_refs has_many VipsExerciseRef
 * @property SimpleORMapCollection<VipsSolution> $solutions has_many VipsSolution
 * @property User $user belongs_to User
 * @property Folder $folder has_one Folder
 * @property SimpleORMapCollection<VipsTest> $tests has_and_belongs_to_many VipsTest
 */
class MatrixChoiceTask extends Exercise
{
    /**
     * Get the icon of this exercise type.
     */
    public static function getTypeIcon(string $role = Icon::DEFAULT_ROLE): Icon
    {
        return Icon::create('task-matrix-choice', $role);
    }

    /**
     * Get a description of this exercise type.
     */
    public static function getTypeDescription(): string
    {
        return _('Einfachauswahl pro Zeile in einer Tabelle');
    }

    /**
     * Initialize a new instance of this class.
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        if (!isset($id)) {
            $this->task['choices'] = [];
        }
    }

    /**
     * Initialize this instance from the current request environment.
     */
    public function initFromRequest($request): void
    {
        parent::initFromRequest($request);

        $this->task['choices'] = [];
        $choice_index = [];

        foreach ($request['choice'] as $i => $choice) {
            if (trim($choice) != '') {
                $this->task['choices'][] = trim($choice);
                $choice_index[$i] = count($choice_index);
            }
        }

        foreach ($request['answer'] as $i => $answer) {
            $answer = self::purifyFlexibleInput($answer);

            if (trim($answer) != '') {
                $this->task['answers'][] = [
                    'text'   => trim($answer),
                    'choice' => $choice_index[$request['correct'][$i]]
                ];
            }
        }

        if ($request['optional']) {
            $this->options['optional'] = 1;
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
     * @param mixed solution The solution XML string as returned by responseFromRequest().
     */
    public function evaluateItems($solution): array
    {
        $result = [];

        $response = $solution->response;

        foreach ($this->task['answers'] as $i => $answer) {
            if (!isset($response[$i]) || $response[$i] === '' || $response[$i] == -1) {
                $points = null;
            } else {
                $points = $response[$i] == $answer['choice'] ? 1 : 0;
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
        return ['MCO-Frage', 'Auswahl', '[+~]?Antwort'];
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
                    'text'   => Studip\Markup::purifyHtml(current($tag)),
                    'choice' => 0
                ];
            } else if (key($tag) === 'Antwort') {
                $this->task['answers'][] = [
                    'text'   => Studip\Markup::purifyHtml(current($tag)),
                    'choice' => 1
                ];
            }
        }

        foreach ($exercise as $tag) {
            if (key($tag) === 'Auswahl') {
                [$label_yes, $label_no] = explode('/', current($tag));
                $this->task['choices'] = [trim($label_yes), trim($label_no)];
            }
        }

        $this->options['optional'] = 1;
    }

    /**
     * Initialize this instance from the given SimpleXMLElement object.
     */
    public function initXML($exercise): void
    {
        parent::initXML($exercise);

        foreach ($exercise->items->item->answers->answer as $answer) {
            if (isset($answer['correct'])) {
                $choice = (int) $answer['correct'];
            } else {
                $choice = (int) $answer['score'] ? 0 : 1;
            }

            $this->task['answers'][] = [
                'text'   => Studip\Markup::purifyHtml(trim($answer)),
                'choice' => $choice
            ];
        }

        foreach ($exercise->items->item->choices->choice as $choice) {
            if ($choice['type'] == 'none') {
                $this->options['optional'] = 1;
            } else {
                $this->task['choices'][] = trim($choice);
            }
        }
    }

    /**
     * Creates a template for editing an exercise.
     */
    public function getEditTemplate(?VipsAssignment $assignment): Flexi\Template
    {
        if (!$this->task['choices']) {
            $this->task['choices'] = [_('Ja'), _('Nein')];
        }

        if (!$this->task['answers']) {
            $this->task['answers'] = array_fill(0, 5, ['text' => '', 'choice' => 0]);
        }

        return parent::getEditTemplate($assignment);
    }

    /**
     * Create a template for viewing an exercise.
     */
    public function getViewTemplate($view, $solution, $assignment, $user_id): Flexi\Template
    {
        $template = parent::getViewTemplate($view, $solution, $assignment, $user_id);

        if (isset($this->options['optional']) && $this->options['optional']) {
            $template->optional_choice = [-1 => _('keine Antwort')];
        } else {
            $template->optional_choice = [];
        }

        return $template;
    }

    /**
     * Export a response for this exercise into an array of strings.
     */
    public function exportResponse(array $response): array
    {
        return array_map(
            fn($a) => $a == -1 ? '' : $a,
            $response
        );
    }
}
