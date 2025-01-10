<?php
/*
 * SequenceTask.php - Vips plugin for Stud.IP
 * Copyright (c) 2022  Elmar Ludwig
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
class SequenceTask extends Exercise
{
    /**
     * Get the icon of this exercise type.
     */
    public static function getTypeIcon(string $role = Icon::DEFAULT_ROLE): Icon
    {
        return Icon::create('hamburger', $role);
    }

    /**
     * Get a description of this exercise type.
     */
    public static function getTypeDescription(): string
    {
        return _('Anordnung von Elementen in einer Reihe');
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
                    'id' => (int) $request['id'][$i],
                    'text' => trim($answer)
                ];
            }
        }

        $this->task['compare'] = $request['compare'];

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
     * Compute the default maximum points which can be reached in this
     * exercise, dependent on the number of answers.
     */
    public function itemCount(): int
    {
        if ($this->task['compare'] === 'sequence') {
            return max(count($this->task['answers']) - 1, 0);
        }

        return count($this->task['answers']);
    }

    /**
     * Return the list of answers as ordered by the student (if applicable).
     */
    public function orderedAnswers($response)
    {
        $answers = $this->task['answers'];
        $pos = isset($response) ? array_flip($response) : [];

        usort($answers, function($a, $b) use ($pos) {
            if (isset($pos[$a['id']]) && isset($pos[$b['id']])) {
                return $pos[$a['id']] <=> $pos[$b['id']];
            } else if (isset($pos[$a['id']])) {
                return -1;
            } else if (isset($pos[$b['id']])) {
                return 1;
            } else {
                return $a['id'] <=> $b['id'];
            }
        });

        return $answers;
    }

    /**
     * Evaluates a student's solution for the individual items in this
     * exercise. Returns an array of ('points' => float, 'safe' => boolean).
     *
     * @param mixed $solution The solution object returned by getSolutionFromRequest().
     */
    public function evaluateItems($solution): array
    {
        $result = [];

        $response = $solution->response;
        $item_count = $this->itemCount();
        $answers = $this->task['answers'];
        $pos = array_flip($response);

        for ($i = 0; $i < $item_count; ++$i) {
            if ($this->task['compare'] === 'sequence') {
                if ($pos[$answers[$i]['id']] + 1 == $pos[$answers[$i + 1]['id']]) {
                    $points = 1;
                } else {
                    $points = 0;
                }
            } else {
                if ($pos[$answers[$i]['id']] == $i) {
                    $points = 1;
                } else {
                    $points = 0;
                }
            }

            if (!$this->task['compare'] && count($result)) {
                $result[0]['points'] &= $points;
            } else {
                $result[] = ['points' => $points, 'safe' => true];
            }
        }

        return $result;
    }

    /**
     * Initialize this instance from the given SimpleXMLElement object.
     */
    public function initXML($exercise): void
    {
        parent::initXML($exercise);

        foreach ($exercise->items->item->answers->answer as $answer) {
            $this->task['answers'][] = [
                'text' => Studip\Markup::purifyHtml(trim($answer))
            ];
        }

        if ($exercise->items->item->{'evaluation-hints'}) {
            switch ($exercise->items->item->{'evaluation-hints'}->similarity['type']) {
                case 'position':
                case 'sequence':
                    $this->task['compare'] = (string) $exercise->items->item->{'evaluation-hints'}->similarity['type'];
            }
        }

        $this->createIds();
    }



    /**
     * Creates a template for editing a SequenceTask.
     */
    public function getEditTemplate(?VipsAssignment $assignment): Flexi\Template
    {
        if (!$this->task['answers']) {
            $this->task['answers'] = array_fill(0, 5, ['id' => '', 'text' => '']);
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

        if ($solution && $solution->id) {
            $template->results = $this->evaluateItems($solution);
        }

        return $template;
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

        foreach ($request['answer'] as $id) {
            $result[] = (int) $id;
        }

        return $result;
    }

    /**
     * Export a response for this exercise into an array of strings.
     */
    public function exportResponse(array $response): array
    {
        $result = [];

        foreach ($response as $id) {
            foreach ($this->task['answers'] as $answer) {
                if ($answer['id'] === $id) {
                    $result[] = $answer['text'];
                }
            }
        }

        return $result;
    }
}
