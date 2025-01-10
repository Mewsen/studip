<?php
/*
 * MatchingTask.php - Vips plugin for Stud.IP
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
class MatchingTask extends Exercise
{
    /**
     * Get the icon of this exercise type.
     */
    public static function getTypeIcon(string $role = Icon::DEFAULT_ROLE): Icon
    {
        return Icon::create('view-list', $role);
    }

    /**
     * Get a description of this exercise type.
     */
    public static function getTypeDescription(): string
    {
        return _('Zuordnung von Elementen zu Kategorien');
    }

    /**
     * Initialize a new instance of this class.
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        if (!isset($id)) {
            $this->task['groups'] = [];
        }
    }

    /**
     * Initialize this instance from the current request environment.
     */
    public function initFromRequest($request): void
    {
        parent::initFromRequest($request);

        $id      = $request['id'];
        $_id     = $request['_id'];

        $this->task['groups'] = [];
        $this->task['select'] = $request['multiple'] ? 'multiple' : 'single';

        foreach ($request['default'] as $i => $group) {
            $group  = self::purifyFlexibleInput($group);
            $answers = (array) $request['answer'][$i];

            if (trim($group) != '') {
                foreach ($answers as $j => $answer) {
                    $answer = self::purifyFlexibleInput($answer);

                    if (trim($answer) != '') {
                        $this->task['answers'][] = [
                            'id' => (int) $id[$i][$j],
                            'text' => trim($answer),
                            'group' => count($this->task['groups'])
                        ];
                    }
                }

                $this->task['groups'][] = trim($group);
            }
        }

        // list of answers that must remain unassigned
        foreach ($request['_answer'] as $i => $answer) {
            $answer = self::purifyFlexibleInput($answer);

            if (trim($answer) != '') {
                $this->task['answers'][] = [
                    'id' => (int) $_id[$i],
                    'text' => trim($answer),
                    'group' => -1
                ];
            }
        }

        $this->createIds();
    }

    /**
     * Genereate new IDs for all answers that do not yet have one.
     */
    public function createIds(): void
    {
        $ids = [0 => true];

        foreach ($this->task['answers'] as $i => &$answer) {
            if (empty($answer['id'])) {
                do {
                    $answer['id'] = rand();
                } while (isset($ids[$answer['id']]));
            }

            $ids[$answer['id']] = true;
        }
    }

    /**
     * Check if multiple assignment mode is enabled for this exercise.
     */
    public function isMultiSelect(): bool
    {
        return isset($this->task['select']) && $this->task['select'] === 'multiple';
    }

    /**
     * Compute the default maximum points which can be reached in this
     * exercise, dependent on the number of answers.
     */
    public function itemCount(): int
    {
        return count($this->task['answers']) - count($this->correctAnswers(-1));
    }

    /**
     * Sort the list of answers by their ids.
     */
    public function sortAnswersById(): void
    {
        usort(
            $this->task['answers'],
            fn($a, $b) => $a['id'] <=> $b['id']
        );
    }

    /**
     * Returns all the correct answers for the given group.
     */
    public function correctAnswers(string $group): array
    {
        $answers = [];

        foreach ($this->task['answers'] as $answer) {
            if ($answer['group'] == $group) {
                $answers[] = $answer['text'];
            }
        }

        return $answers;
    }

    /**
     * Check if this answer is a correct assignment to the given group.
     */
    public function isCorrectAnswer(array $answer, string $group): bool
    {
        if ($answer['group'] == $group) {
            return true;
        }

        foreach ($this->task['answers'] as $_answer) {
            if ($_answer['group'] == $group) {
                if ($answer['text'] === $_answer['text']) {
                    return true;
                }
            }
        }

        return false;
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
        $item_count = $this->itemCount();

        foreach ($this->task['answers'] as $answer) {
            $group = $response[$answer['id']] ?? -1;

            if ($group != -1) {
                $points = $this->isCorrectAnswer($answer, $group) ? 1 : 0;
                $result[] = ['points' => $points, 'safe' => true];
            }
        }

        // assign no points for missing answers
        while (count($result) < $item_count) {
            $result[] = ['points' => 0, 'safe' => true];
        }

        return $result;
    }

    /**
     * Return the list of keywords used for text export. The first keyword
     * in the list must be the keyword for the exercise type.
     */
    public static function getTextKeywords(): array
    {
        return ['ZU-Frage', 'Vorgabe', 'Antwort', 'Distraktor'];
    }

    /**
     * Initialize this instance from the given text data array.
     */
    public function initText(array $exercise): void
    {
        parent::initText($exercise);

        foreach ($exercise as $tag) {
            if (key($tag) === 'Vorgabe') {
                $group = count($this->task['groups']);
                $this->task['groups'][] = Studip\Markup::purifyHtml(current($tag));
            }

            if (key($tag) === 'Antwort' && isset($group)) {
                $this->task['answers'][] = [
                    'text'  => Studip\Markup::purifyHtml(current($tag)),
                    'group' => $group
                ];
                unset($group);
            }

            if (key($tag) === 'Distraktor') {
                $this->task['answers'][] = [
                    'text'  => Studip\Markup::purifyHtml(current($tag)),
                    'group' => -1
                ];
            }
        }

        $this->createIds();
    }


    /**
     * Initialize this instance from the given SimpleXMLElement object.
     */
    public function initXML($exercise): void
    {
        parent::initXML($exercise);

        $this->task['select'] = $exercise->items->item['type'] == 'matching-multiple' ? 'multiple' : 'single';

        foreach ($exercise->items->item->choices->choice as $choice) {
            $this->task['groups'][] = Studip\Markup::purifyHtml(trim($choice));
        }

        foreach ($exercise->items->item->answers->answer as $answer) {
            $this->task['answers'][] = [
                'text'  => Studip\Markup::purifyHtml(trim($answer)),
                'group' => (int) $answer['correct']
            ];
        }

        $this->createIds();
    }



    /**
     * Creates a template for editing a MatchingTask.
     */
    public function getEditTemplate(?VipsAssignment $assignment): \Flexi\Template
    {
        if (!$this->task['answers']) {
            foreach (range(0, 4) as $i) {
                $this->task['answers'][] = ['id' => '', 'text' => '', 'group' => count($this->task['groups'])];
                $this->task['groups'][] = '';
            }
        }

        return parent::getEditTemplate($assignment);
    }

    /**
     * Return the solution of the student from the request POST data.
     *
     * @param array $request array containing the postdata for the solution.
     * @return array containing the solutions of the student.
     */
    public function responseFromRequest(array|ArrayAccess $request): array
    {
        $result = [];

        foreach ($this->task['answers'] as $answer) {
            // get the group the user has added this answer to
            $result[$answer['id']] = (int) $request['answer'][$answer['id']];
        }

        return $result;
    }

    /**
     * Export a response for this exercise into an array of strings.
     */
    public function exportResponse(array $response): array
    {
        $result = [];

        foreach ($this->task['answers'] as $answer) {
            if ($answer['group'] != -1) {
                if (isset($response[$answer['id']]) && $response[$answer['id']] != -1) {
                    $result[] = $this->task['groups'][$response[$answer['id']]];
                } else {
                    $result[] = '';
                }
            }
        }

        return $result;
    }
}
