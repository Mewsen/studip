<?php
/*
 * SingleChoiceTask.php - Vips plugin for Stud.IP
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
class SingleChoiceTask extends Exercise
{
    /**
     * Get the icon of this exercise type.
     */
    public static function getTypeIcon(string $role = Icon::DEFAULT_ROLE): Icon
    {
        return Icon::create('task-single-choice', $role);
    }

    /**
     * Get a description of this exercise type.
     */
    public static function getTypeDescription(): string
    {
        return _('Einfachauswahl aus einer Liste');
    }

    /**
     * Initialize a new instance of this class.
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        if (!isset($id)) {
            $this->task = [];
        }
    }

    /**
     * Initialize this instance from the current request environment.
     */
    public function initFromRequest($request): void
    {
        parent::initFromRequest($request);

        $this->task = [];

        foreach ($request['answer'] as $group => $answergroup) {
            $task = [];
            $description = trim($request['description'][$group]);
            $description = Studip\Markup::purifyHtml($description);

            if ($this->task && $description != '') {
                $task['description'] = $description;
            }

            foreach ($answergroup as $i => $answer) {
                $answer = self::purifyFlexibleInput($answer);

                if (trim($answer) != '') {
                    $task['answers'][] = [
                        'text'  => trim($answer),
                        'score' => $request['correct'][$group] == $i ? 1 : 0
                    ];
                }
            }

            if ($task) {
                $this->task[] = $task;
            }
        }

        if ($request['optional']) {
            $this->options['optional'] = 1;
        }
    }

    /**
     * Computes the default maximum points which can be reached in this
     * exercise, dependent on the number of groups.
     *
     * @return int maximum points
     */
    public function itemCount(): int
    {
        return count($this->task);
    }

    /**
     * Shuffle the answer alternatives.
     *
     * @param $user_id string used for initialising the randomizer.
     */
    public function shuffleAnswers(string $user_id): void
    {
        srand(crc32($this->id . ':' . $user_id));

        for ($block = 0; $block < count($this->task); $block++) {
            $random_order = range(0, count($this->task[$block]['answers']) - 1);
            shuffle($random_order);

            $answer_temp = [];
            foreach ($random_order as $index) {
                $answer_temp[$index] = $this->task[$block]['answers'][$index];
            }
            $this->task[$block]['answers'] = $answer_temp;
        }

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

        foreach ($this->task as $i => $task) {
            if (!isset($response[$i]) || $response[$i] === '' || $response[$i] == -1) {
                $points = null;
            } else {
                $points = $task['answers'][$response[$i]]['score'];
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
        return ['SCO?-Frage|JN-Frage', '[+~]?Antwort'];
    }

    /**
     * Initialize this instance from the given text data array.
     */
    public function initText(array $exercise): void
    {
        parent::initText($exercise);

        $block = 0;

        foreach ($exercise as $tag) {
            if (key($tag) === 'Type' && current($tag) === 'SCO-Frage') {
                $this->options['optional'] = 1;
            }

            if (key($tag) === '+Antwort' || key($tag) === 'Antwort') {
                if (preg_match('/\n--$/', current($tag))) {
                    $text = trim(substr(current($tag), 0, -3));
                    $incr = 1;
                } else {
                    $text = current($tag);
                    $incr = 0;
                }

                $score = key($tag) === '+Antwort' ? 1 : 0;

                $this->task[$block]['answers'][] = [
                    'text'  => Studip\Markup::purifyHtml($text),
                    'score' => $score
                ];

                $block += $incr;
            }
        }
    }

    /**
     * Initialize this instance from the given SimpleXMLElement object.
     */
    public function initXML($exercise): void
    {
        parent::initXML($exercise);

        foreach ($exercise->items->item as $item) {
            $task = [];

            if ($item->description) {
                $task['description'] = Studip\Markup::purifyHtml(trim($item->description->text));
            }

            foreach ($item->answers->answer as $answer) {
                if ($answer['default'] == 'true') {
                    $this->options['optional'] = 1;
                } else {
                    $task['answers'][] = [
                        'text'  => Studip\Markup::purifyHtml(trim($answer)),
                        'score' => (int) $answer['score']
                    ];
                }
            }

            $this->task[] = $task;
        }
    }

    /**
     * Creates a template for editing a SingleChoiceTask.
     */
    public function getEditTemplate(?VipsAssignment $assignment): Flexi\Template
    {
        if (!$this->task) {
            $this->task[0]['answers'] = array_fill(0, 5, ['text' => '', 'score' => 0]);
        }

        return parent::getEditTemplate($assignment);
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
        $template = parent::getViewTemplate($view, $solution, $assignment, $user_id);

        if (isset($this->options['optional']) && $this->options['optional']) {
            $template->optional_answer = [-1 => ['text' => _('keine Antwort'), 'score' => 0]];
        } else {
            $template->optional_answer = [];
        }

        return $template;
    }

    /**
     * Export a response for this exercise into an array of strings.
     */
    public function exportResponse(array $response): array
    {
        return array_map(function($a) { return $a == -1 ? '' : $a; }, $response);
    }
}
