<?php
/*
 * ClozeTask.php - Vips plugin for Stud.IP
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
class ClozeTask extends Exercise
{
    /**
     * Get the icon of this exercise type.
     */
    public static function getTypeIcon(string $role = Icon::DEFAULT_ROLE): Icon
    {
        return Icon::create('task-cloze', $role);
    }

    /**
     * Get a description of this exercise type.
     */
    public static function getTypeDescription(): string
    {
        return _('Lückentext mit Eingabe oder Auswahl');
    }

    /**
     * Initialize a new instance of this class.
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        if (!isset($id)) {
            $this->task['text'] = '';
        }
    }

    /**
     * Initialize this instance from the current request environment.
     */
    public function initFromRequest($request): void
    {
        parent::initFromRequest($request);

        $this->parseClozeText(trim($request['cloze_text']));
        $this->task['compare'] = $request['compare'];

        if ($this->task['compare'] === 'numeric') {
            $this->task['epsilon'] = (float) strtr($request['epsilon'], ',', '.') / 100;
        }

        if (isset($request['input_width'])) {
            $this->task['input_width'] = (int) $request['input_width'];
        }

        if ($request['layout']) {
            $this->task['layout'] = $request['layout'];
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
     * Return the list of keywords used for text export. The first keyword
     * in the list must be the keyword for the exercise type.
     */
    public static function getTextKeywords(): array
    {
        return ["L'text", 'Eingabehilfe', 'Abgleich'];
    }

    /**
     * Initialize this instance from the given text data array.
     */
    public function initText(array $exercise): void
    {
        parent::initText($exercise);

        $this->parseClozeText($this->description);
        $this->description = '';

        foreach ($exercise as $tag) {
            if (key($tag) === 'Abgleich') {
                if (current($tag) === 'Kleinbuchstaben') {
                    $this->task['compare'] = 'ignorecase';
                }
            }
        }
    }

    /**
     * Initialize this instance from the given SimpleXMLElement object.
     */
    public function initXML($exercise): void
    {
        parent::initXML($exercise);
        $this->task['text'] = '';
        $select = null;

        foreach ($exercise->items->item->description->children() as $name => $elem) {
            if ($name == 'text') {
                $this->task['text'] .= (string) $elem;
            } else if ($name == 'answers') {
                $answers = [];

                foreach ($elem->answer as $answer) {
                    $answers[] = [
                        'text'  => (string) $answer,
                        'score' => (string) $answer['score']
                    ];
                }

                if ($elem['select'] == 'true') {
                    $select[] = $this->itemCount();
                }

                $this->task['answers'][] = $answers;
                $this->task['text'] .= '[[]]';
            }
        }

        $this->task['text'] = Studip\Markup::purifyHtml($this->task['text']);

        switch ($exercise->items->item['type']) {
            case 'cloze-input':
                $this->task['select'] = $select;
                break;
            case 'cloze-select':
                $this->task['layout'] = 'select';
                break;
            case 'cloze-drag':
                $this->task['layout'] = 'drag';
        }

        if ($exercise->items->item->{'submission-hints'}) {
            if ($exercise->items->item->{'submission-hints'}->input['width']) {
                $this->task['input_width'] = (int) $exercise->items->item->{'submission-hints'}->input['width'];
            }
        }

        if ($exercise->items->item->{'evaluation-hints'}) {
            switch ($exercise->items->item->{'evaluation-hints'}->similarity['type']) {
                case 'ignorecase':
                    $this->task['compare'] = 'ignorecase';
                    break;
                case 'numeric':
                    $this->task['compare'] = 'numeric';
                    $this->task['epsilon'] = (float) $exercise->items->item->{'evaluation-hints'}->{'input-data'};
            }
        }
    }

    /**
     * Creates a template for editing a cloze exercise. NOTE: As a cloze
     * exercise has no special fields (it consists only of the question),
     * normally, an empty template will be returned. The only elements it can
     * contain are message boxes alerting that for the same cloze an answer
     * alternative has been set repeatedly.
     */
    public function getEditTemplate(?VipsAssignment $assignment): Flexi\Template
    {
        $duplicate_alternatives = $this->findDuplicateAlternatives();

        foreach ($duplicate_alternatives as $alternative) {
            $message = sprintf(_('Achtung: Sie haben bei der %d. Lücke die Antwort &bdquo;%s&ldquo; mehrfach eingetragen.'),
                               $alternative['index'] + 1, htmlReady($alternative['text']));
            PageLayout::postWarning($message);
        }

        return parent::getEditTemplate($assignment);
    }

    /**
     * Create a template for viewing an exercise.
     */
    public function getViewTemplate($view, $solution, $assignment, $user_id): \Flexi\Template
    {
        $template = parent::getViewTemplate($view, $solution, $assignment, $user_id);

        if ($solution && $solution->id) {
            $template->results = $this->evaluateItems($solution);
        }

        return $template;
    }

    /**
     * Return the interaction type of this task (input, select or drag).
     */
    public function interactionType(): string
    {
        return $this->task['layout'] ?? 'input';
    }

    /**
     * Check if selection should be offered for the given item.
     */
    public function isSelect(string $item, bool $use_default = true): bool
    {
        if ($use_default && $this->interactionType() === 'select') {
            return true;
        }

        if (isset($this->task['select'])) {
            return in_array($item, $this->task['select']);
        }

        return false;
    }

    /**
     * Returns all currently unassigned answers for the given solution.
     */
    public function availableAnswers(?VipsSolution $solution): array
    {
        $answers = [];
        $response = $solution->response ?? [];

        foreach ($this->task['answers'] as $answer) {
            foreach ($answer as $option) {
                $i = array_search($option['text'], $response);

                if ($i !== false) {
                    unset($response[$i]);
                } else if ($option['text'] !== '') {
                    $answers[] = $option['text'];
                }
            }
        }

        sort($answers, SORT_LOCALE_STRING);
        return $answers;
    }

    /**
     * Returns all the correct answers for an item in an array.
     */
    public function correctAnswers($item): array
    {
        $answers = [];

        foreach ($this->task['answers'][$item] as $answer) {
            if ($answer['score'] == 1) {
                $answers[] = $answer['text'];
            }
        }

        return $answers;
    }

    /**
     * Calculate the optimal input field size for text exercises.
     *
     * @param int $item item number
     * @return int length of input field in characters
     */
    public function getInputWidth($item): int
    {
        if (isset($this->task['input_width'])) {
            return 5 << $this->task['input_width'];
        }

        $max = 0;

        foreach ($this->task['answers'][$item] as $option) {
            $length = mb_strlen($option['text']);

            if ($length > $max) {
                $max = $length;
            }
        }

        $length = $max ? min(max($max, 6), 48) : 12;

        // possible sizes: 5, 10, 20, 40
        return 5 << ceil(log($length / 6) / log(2));
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
        $ignorecase = isset($this->task['compare']) && $this->task['compare'] === 'ignorecase';
        $numeric = isset($this->task['compare']) && $this->task['compare'] === 'numeric';

        foreach ($this->task['answers'] as $blank => $answer) {
            $student_answer = $this->normalizeText($response[$blank] ?? '', $ignorecase);
            $options = ['' => 0];
            $points = 0;
            $safe = $this->interactionType() !== 'input';

            foreach ($answer as $option) {  // different answer options
                if ($numeric && $student_answer !== '') {
                    $correct_unit = $student_unit = null;
                    $correct = $this->normalizeFloat($option['text'], $correct_unit);
                    $student = $this->normalizeFloat($response[$blank], $student_unit);

                    if ($correct_unit === $student_unit) {
                        if (abs($correct - $student) <= abs($correct * $this->task['epsilon'])) {
                            $options[$student_answer] = max($option['score'], $options[$student_answer]);
                        } else {
                            $safe = true;
                        }
                    }
                } else {
                    $content = $this->normalizeText($option['text'], $ignorecase);
                    $options[$content] = $option['score'];
                }
            }

            if (isset($options[$student_answer])) {
                $points = $options[$student_answer];
                $safe = true;
            }

            $result[] = ['points' => $points, 'safe' => $safe];
        }

        return $result;
    }



    #######################################
    #                                     #
    #   h e l p e r   f u n c t i o n s   #
    #                                     #
    #######################################



    /**
     * Returns the exercise for the lecturer. Clozes are represented by square brackets.
     */
    public function getClozeText(): string
    {
        $is_html = Studip\Markup::isHtml($this->task['text']);
        $result = '';

        foreach (explode('[[]]', $this->task['text']) as $blank => $text) {
            $result .= $text;

            if (isset($this->task['answers'][$blank])) {  // blank
                $answers = [];
                $select = $this->isSelect($blank, false) ? ':' : '';

                foreach ($this->task['answers'][$blank] as $answer) {
                    $answer_text = $answer['text'];

                    if (preg_match('/^$|^[":*~ ]|\||\]\]|[] ]$/', $answer_text)) {
                        $answer_text = '"' . $answer_text . '"';
                    }

                    if ($answer['score'] == 0) {
                        $answers[] = '*' . $answer_text;
                    } else if ($answer['score'] == 0.5) {
                        $answers[] = '~' . $answer_text;
                    } else {
                        $answers[] = $answer_text;
                    }
                }

                $blank = '[[' . $select . implode('|', $answers) . ']]';

                if ($is_html) {
                    $blank = htmlReady($blank);
                }

                $result .= $blank;
            }
        }

        return $result;
    }



    /**
     * Converts plain text ("foo bar [blank] text...") to array.
     */
    public function parseClozeText(string $question): void
    {
        $is_html = Studip\Markup::isHtml($question);
        $question = Studip\Markup::purifyHtml($question);
        $this->task['text'] = '';

        // $question_array contains text elements and blanks (surrounded by [[ and ]]).
        $parts = preg_split('/(\[\[(?:".*?"|.)*?\]\])/s', $question, -1, PREG_SPLIT_DELIM_CAPTURE);
        $select = null;

        foreach ($parts as $part) {
            if (preg_match('/^\[\[(.*)\]\]$/s', $part, $matches)) {
                $part = preg_replace("/[\t\n\r\xA0]/", ' ', $matches[1]);
                $answers = [];

                if ($is_html) {
                    $part = Studip\Markup::markAsHtml($part);
                    $part = Studip\Markup::removeHtml($part);
                }

                if ($part[0] === ':') {
                    $select[] = $this->itemCount();
                    $part = substr($part, 1);
                }

                if ($part !== '') {
                    preg_match_all('/((?:".*?"|[^|])*)\|/', $part . '|', $matches);

                    foreach ($matches[1] as $answer) {
                        $answer = trim($answer);
                        $points = 1;

                        if ($answer !== '') {
                            if ($answer[0] === '*') {
                                $points = 0;
                                $answer = substr($answer, 1);
                            } else if ($answer[0] === '~') {
                                $points = 0.5;
                                $answer = substr($answer, 1);
                            }
                        }

                        if (preg_match('/^"(.*)"$/', $answer, $matches)) {
                            $answer = $matches[1];
                        }

                        $answers[] = ['text' => $answer, 'score' => $points];
                    }
                }

                $this->task['answers'][] = $answers;
                $this->task['text'] .= '[[]]';
            } else {
                $this->task['text'] .= $part;
            }
        }

        $this->task['select'] = $select;
    }

    /**
     * Searches in each cloze if an answer alternative is given repatedly.
     *
     * @return array Either an empty array or an array of arrays, each containing the
     *          elements 'index' (index of the cloze where the duplicate
     *          entry was found) and 'text' (text of the duplicate entry).
     */
    private function findDuplicateAlternatives(): array
    {
        $duplicate_alternatives = [];

        foreach ($this->task['answers'] as $index => $answers) {
            $alternatives = [];

            foreach ($answers as $answer) {
                if (in_array($answer['text'], $alternatives, true)) {
                    $duplicate_alternatives[] = [
                        'index' => $index,
                        'text'  => $answer['text']
                    ];
                }

                $alternatives[] = $answer['text'];
            }
        }

        return $duplicate_alternatives;
    }
}
