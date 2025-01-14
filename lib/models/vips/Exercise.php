<?php
/*
 * Exercise.php - base class for all exercise types
 * Copyright (c) 2006-2009  Elmar Ludwig, Martin Schröder
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

abstract class Exercise extends SimpleORMap
{
    /**
     * The unpacked value from the "task" column in the SORM instance.
     * This is an array, but type hinting does not work due to SORM
     * writing the JSON string into this property on restore().
     */
    public $task = [];

    /**
     * @var array<class-string<static>, array>
     */
    private static array $exercise_types = [];

    /**
     * Configure the database mapping.
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'etask_tasks';

        $config['serialized_fields']['options'] = JSONArrayObject::class;

        $config['has_and_belongs_to_many']['tests'] = [
            'class_name'        => VipsTest::class,
            'thru_table'        => 'etask_test_tasks',
            'thru_key'          => 'task_id',
            'thru_assoc_key'    => 'test_id'
        ];

        $config['has_many']['exercise_refs'] = [
            'class_name'        => VipsExerciseRef::class,
            'assoc_foreign_key' => 'task_id'
        ];
        $config['has_many']['solutions'] = [
            'class_name'        => VipsSolution::class,
            'assoc_foreign_key' => 'task_id',
            'on_delete'         => 'delete'
        ];

        $config['has_one']['folder'] = [
            'class_name'        => Folder::class,
            'assoc_foreign_key' => 'range_id',
            'assoc_func'        => 'findByRangeIdAndFolderType',
            'foreign_key'       => fn($record) => [$record->id, 'ExerciseFolder'],
            'on_delete'         => 'delete'
        ];

        $config['belongs_to']['user'] = [
            'class_name'  => User::class,
            'foreign_key' => 'user_id'
        ];

        parent::configure($config);
    }

    /**
     * Initialize a new instance of this class.
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        if (!isset($id)) {
            $this->type = get_class($this);
            $this->task = ['answers' => []];
        }

        if (is_null($this->options)) {
            $this->options = [];
        }
    }

    /**
     * Initialize this instance from the current request environment.
     */
    public function initFromRequest($request): void
    {
        $this->title       = trim($request['exercise_name']);
        $this->description = trim($request['exercise_question']);
        $this->description = Studip\Markup::purifyHtml($this->description);
        $exercise_hint     = trim($request['exercise_hint']);
        $exercise_hint     = Studip\Markup::purifyHtml($exercise_hint);
        $feedback          = trim($request['feedback']);
        $feedback          = Studip\Markup::purifyHtml($feedback);
        $this->task        = ['answers' => []];
        $this->options     = [];

        if ($this->title === '') {
            $this->title = _('Aufgabe');
        }

        if ($exercise_hint !== '') {
            $this->options['hint'] = $exercise_hint;
        }

        if ($feedback !== '') {
            $this->options['feedback'] = $feedback;
        }

        if ($request['exercise_comment']) {
            $this->options['comment'] = 1;
        }

        if ($request['file_ids'] && !$request['files_visible']) {
            $this->options['files_hidden'] = 1;
        }
    }

    /**
     * Filter input from flexible input with HTMLPurifier (if required).
     */
    public static function purifyFlexibleInput(string $html): string
    {
        if (Studip\Markup::isHtml($html)) {
            $text = Studip\Markup::removeHtml($html);

            if (substr_count($html, '<') > 1 || kill_format($text) !== $text) {
                $html = Studip\Markup::purifyHtml($html);
            } else {
                $html = $text;
            }
        }

        return $html;
    }

    /**
     * Load a specific exercise from the database.
     */
    public static function find($id)
    {
        $db = DBManager::get();

        $stmt = $db->prepare('SELECT * FROM etask_tasks WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return self::buildExisting($data);
        }

        return null;
    }

    /**
     * Load an array of exercises filtered by given sql from the database.
     *
     * @param string $sql clause to use on the right side of WHERE
     * @param array $params for query
     */
    public static function findBySQL($sql, $params = [])
    {
        $db = DBManager::get();

        $has_join = stripos($sql, 'JOIN ');
        if ($has_join === false || $has_join > 10) {
            $sql = 'WHERE ' . $sql;
        }
        $stmt = $db->prepare('SELECT etask_tasks.* FROM etask_tasks ' . $sql);
        $stmt->execute($params);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = [];

        while ($data = $stmt->fetch()) {
            $result[] = self::buildExisting($data);
        }

        return $result;
    }

    /**
     * Find related records for an n:m relation (has_and_belongs_to_many)
     * using a combination table holding the keys.
     *
     * @param string $foreign_key_value value of foreign key to find related records
     * @param array $options relation options from other side of relation
     */
    public static function findThru($foreign_key_value, $options)
    {
        $thru_table = $options['thru_table'];
        $thru_key = $options['thru_key'];
        $thru_assoc_key = $options['thru_assoc_key'];

        $sql = "JOIN `$thru_table` ON `$thru_table`.`$thru_assoc_key` = etask_tasks.id
                WHERE `$thru_table`.`$thru_key` = ? " . $options['order_by'];

        return self::findBySQL($sql, [$foreign_key_value]);
    }

    /**
     * Create a new exercise object from a data array.
     */
    public static function create($data)
    {
        $class = class_exists($data['type']) ? $data['type'] : DummyExercise::class;

        if (static::class === self::class) {
            return $class::create($data);
        } else {
            return parent::create($data);
        }
    }

    /**
     * Build an exercise object from a data array.
     */
    public static function buildExisting($data)
    {
        $class = class_exists($data['type']) ? $data['type'] : DummyExercise::class;

        return $class::build($data, false);
    }

    /**
     * Initialize task structure from JSON string.
     */
    public function setTask(mixed $value): void
    {
        if (is_string($value)) {
            $this->content['task'] = $value;
            $value = json_decode($value, true) ?: [];
        }

        $this->task = $value;
    }

    /**
     * Restore this exercise from the database.
     */
    public function restore()
    {
        $result = parent::restore();
        $this->setTask($this->task);

        return $result;
    }

    /**
     * Store this exercise into the database.
     */
    public function store()
    {
        $this->content['task'] = json_encode($this->task);

        return parent::store();
    }

    /**
     * Compute the default maximum points which can be reached in this
     * exercise, dependent on the number of answers (defaults to 1).
     */
    public function itemCount(): int
    {
        return 1;
    }

    /**
     * Overwrite this function for each exercise type where shuffling answer
     * alternatives makes sense.
     *
     * @param string $user_id A value for initialising the randomizer.
     */
    public function shuffleAnswers(string $user_id): void
    {
    }

    /**
     * Returns true if this exercise type is considered as multiple choice.
     * In this case, the evaluation mode set on the assignment is applied.
     */
    public function isMultipleChoice(): bool
    {
        return false;
    }

    /**
     * Evaluates a student's solution for the individual items in this
     * exercise. Returns an array of ('points' => float, 'safe' => boolean).
     *
     * @param VipsSolution $solution The solution object returned by getSolutionFromRequest().
     */
    public abstract function evaluateItems(VipsSolution $solution): array;

    /**
     * Evaluates a student's solution.
     *
     * @param VipsSolution $solution The solution object returned by getSolutionFromRequest().
     */
    public function evaluate(VipsSolution $solution): array
    {
        $results = $this->evaluateItems($solution);
        $mc_mode = $solution->assignment->options['evaluation_mode'];
        $malus   = 0;
        $points  = 0;
        $safe    = true;

        foreach ($results as $item) {
            if ($item['points'] === 0) {
                ++$malus;
            } else if ($item['points'] !== null) {
                $points += $item['points'];
            }

            if ($item['safe'] === null) {
                $safe = null;
            } else if ($safe !== null) {
                // only true if all items are marked as 'safe'
                $safe &= $item['safe'];
            }
        }

        if ($this->isMultipleChoice()) {
            if ($mc_mode == 1) {
                $points = max($points - $malus, 0);
            } else if ($mc_mode == 2 && $malus > 0) {
                $points = 0;
            }
        }

        $percent = $points / max(count($results), 1);

        return ['percent' => $percent, 'safe' => $safe];
    }

    /**
     * Return the default response when there is no existing solution.
     */
    public function defaultResponse(): array
    {
        return array_fill(0, $this->itemCount(), '');
    }

    /**
     * Return the response of the student from the request POST data.
     *
     * @param array $request array containing the postdata for the solution.
     */
    public function responseFromRequest(array|ArrayAccess $request): array
    {
        $result = [];

        for ($i = 0; $i < $this->itemCount(); ++$i) {
            $result[] = trim($request['answer'][$i] ?? '');
        }

        return $result;
    }

    /**
     * Export a response for this exercise into an array of strings.
     */
    public function exportResponse(array $response): array
    {
        return array_values($response);
    }

    /**
     * Export this exercise to Vips XML format.
     */
    public function getXMLTemplate(VipsAssignment $assignment): Flexi\Template
    {
        return $this->getViewTemplate('xml', null, $assignment, null);
    }

    /**
     * Exercise handler to be called when a solution is corrected.
     */
    public function correctSolutionAction(Trails\Controller$controller, VipsSolution $solution): void
    {
    }

    /**
     * Return a URL to a specified route in this exercise class.
     * $params can contain optional additional parameters.
     */
    public function url_for($path, $params = []): string
    {
        $params['exercise_id'] = $this->id;

        return URLHelper::getURL('dispatch.php/vips/sheets/relay/' . $path, $params);
    }

    /**
     * Return an encoded URL to a specified route in this exercise class.
     * $params can contain optional additional parameters.
     */
    public function link_for($path, $params = []): string
    {
        return htmlReady($this->url_for($path, $params));
    }

    /**
     * Create a template for editing an exercise.
     */
    public function getEditTemplate(?VipsAssignment $assignment): Flexi\Template
    {
        $template = VipsModule::$template_factory->open('exercises/' . $this->type . '/edit');
        $template->exercise = $this;

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
        if ($assignment->isShuffled() && $user_id) {
            $this->shuffleAnswers($user_id);
        }

        $template = VipsModule::$template_factory->open('exercises/' . $this->type . '/' . $view);
        $template->exercise = $this;
        $template->solution = $solution;
        $template->response = $solution ? $solution->response : null;
        $template->evaluation_mode = $assignment->options['evaluation_mode'];

        return $template;
    }

    /**
     * Return a template for solving an exercise.
     */
    public function getSolveTemplate(
        ?VipsSolution $solution,
        VipsAssignment $assignment,
        ?string $user_id
    ): Flexi\Template {
        return $this->getViewTemplate('solve', $solution, $assignment, $user_id);
    }

    /**
     * Return a template for correcting an exercise.
     */
    public function getCorrectionTemplate(VipsSolution $solution): Flexi\Template
    {
        return $this->getViewTemplate('correct', $solution, $solution->assignment, $solution->user_id);
    }

    /**
     * Return a template for printing an exercise.
     */
    public function getPrintTemplate(VipsSolution $solution, VipsAssignment $assignment, ?string $user_id)
    {
        return $this->getViewTemplate('print', $solution, $assignment, $user_id);
    }

    /**
     * Get the name of this exercise type.
     */
    public function getTypeName(): string
    {
        return self::$exercise_types[$this->type]['name'];
    }

    /**
     * Get the icon of this exercise type.
     */
    public static function getTypeIcon(string $role = Icon::DEFAULT_ROLE): Icon
    {
        return Icon::create('question-circle', $role);
    }

    /**
     * Get a description of this exercise type.
     */
    public static function getTypeDescription(): string
    {
        return '';
    }

    /**
     * Get the list of supported exercise types.
     */
    public static function getExerciseTypes(): array
    {
        return self::$exercise_types;
    }

    /**
     * Register a new exercise type and class.
     *
     * @param class-string<static> $class
     */
    public static function addExerciseType(string $name, string $class, mixed $type = null): void
    {
        self::$exercise_types[$class] = compact('name', 'type');
    }

    /**
     * Return the list of keywords used for legacy text export. The first
     * keyword in the list must be the keyword for the exercise type.
     */
    public static function getTextKeywords(): array
    {
        return [];
    }

    /**
     * Import a new exercise from text data array.
     */
    public static function importText(string $segment): static
    {
        $all_keywords = ['Tipp'];

        $types = [];
        foreach (self::$exercise_types as $key => $value) {
            $keywords = $key::getTextKeywords();

            if ($keywords) {
                $all_keywords = array_merge($all_keywords, $keywords);
                $types[$key] = array_shift($keywords);
            }
        }

        $type = '';
        $pattern = implode('|', array_unique($all_keywords));
        $parts = preg_split("/\n($pattern):/", $segment, -1, PREG_SPLIT_DELIM_CAPTURE);
        $title = array_shift($parts);

        $exercise = [['Name' => trim($title)]];

        if ($parts) {
            $type = array_shift($parts);
            $text = array_shift($parts);
            $text = preg_replace('/\\\\' . $type . '$/', '', trim($text));

            $exercise[] = ['Type' => trim($type)];
            $exercise[] = ['Text' => trim($text)];
        }

        while ($parts) {
            $tag = array_shift($parts);
            $val = array_shift($parts);
            $val = preg_replace('/\\\\' . $tag . '$/', '', trim($val));

            $exercise[] = [$tag => trim($val)];
        }

        foreach ($types as $key => $value) {
            if (preg_match('/^' . $value . '$/', $type)) {
                $exercise_type = $key;
            }
        }

        if (!isset($exercise_type)) {
            throw new InvalidArgumentException(_('Unbekannter Aufgabentyp: ') . $type);
        }

        /** @var class-string<static> $exercise_type */
        $result = new $exercise_type();
        $result->initText($exercise);
        return $result;
    }

    /**
     * Import a new exercise from Vips XML format.
     */
    public static function importXML($exercise): static
    {
        $type = (string) $exercise->items->item[0]['type'];

        foreach (self::$exercise_types as $key => $value) {
            if ($type === $value['type'] || is_array($value['type']) && in_array($type, $value['type'])) {
                $exercise_type = $key;
            }
        }

        if (!isset($exercise_type)) {
            throw new InvalidArgumentException(_('Unbekannter Aufgabentyp: ') . $type);
        }

        if (
            $exercise_type === MultipleChoiceTask::class
            && $exercise->items->item[0]->choices
        ) {
            $exercise_type = MatrixChoiceTask::class;
        }

        /** @var class-string<static> $exercise_type */
        $result = new $exercise_type();
        $result->initXML($exercise);
        return $result;
    }

    /**
     * Initialize this instance from the given text data array.
     */
    public function initText(array $exercise): void
    {
        foreach ($exercise as $tag) {
            if (key($tag) === 'Name') {
                $this->title = current($tag) ?: _('Aufgabe');
            }

            if (key($tag) === 'Text') {
                $this->description = Studip\Markup::purifyHtml(current($tag));
            }

            if (key($tag) === 'Tipp') {
                $this->options['hint'] = Studip\Markup::purifyHtml(current($tag));
            }
        }
    }

    /**
     * Initialize this instance from the given SimpleXMLElement object.
     */
    public function initXML($exercise): void
    {
        $this->title = trim($exercise->title);

        if ($this->title === '') {
            $this->title = _('Aufgabe');
        }

        if ($exercise->description) {
            $this->description = Studip\Markup::purifyHtml(trim($exercise->description));
        }

        if ($exercise->hint) {
            $this->options['hint'] = Studip\Markup::purifyHtml(trim($exercise->hint));
        }

        if ($exercise['feedback'] == 'true') {
            $this->options['comment'] = 1;
        }

        if ($exercise->{'file-refs'}['hidden'] == 'true') {
            $this->options['files_hidden'] = 1;
        }

        if ($exercise->items->item[0]->feedback) {
            $this->options['feedback'] = Studip\Markup::purifyHtml(trim($exercise->items->item[0]->feedback));
        }
    }

    /**
     * Construct a new solution object from the request post data.
     */
    public function getSolutionFromRequest($request, ?array $files = null): VipsSolution
    {
        $solution = new VipsSolution();
        $solution->exercise = $this;
        $solution->user_id = $GLOBALS['user']->id;
        $solution->response = $this->responseFromRequest($request);
        $solution->student_comment = trim($request['student_comment']);

        return $solution;
    }

    /**
     * Include files referenced by URL into the exercise attachments and
     * rewrite all corresponding URLs in the exercise text.
     */
    public function includeFilesForExport(): void
    {
        if (!$this->folder || count($this->folder->file_refs) === 0) {
            $this->options['files_hidden'] = 1;
        }

        $this->description = $this->rewriteLinksForExport($this->description);
        $this->options['hint'] = $this->rewriteLinksForExport($this->options['hint']);
        $this->task = $this->rewriteLinksForExport($this->task);
    }

    /**
     * Return a normalized version of a string
     *
     * @param string  $string    string to be normalized
     * @param boolean $lowercase make string lower case
     * @return string The normalized string
     */
    protected function normalizeText(string $string, bool $lowercase = true): string
    {
        // remove leading/trailing spaces
        $string = trim($string);

        // compress white space
        $string = preg_replace('/\s+/u', ' ', $string);

        // delete blanks before and after [](){}:;,.!?"=<>^*/+-
        $string = preg_replace('/ *([][(){}:;,.!?"=<>^*\/+-]) */', '$1', $string);

        // convert to lower case if requested
        return $lowercase ? mb_strtolower($string) : $string;
    }

    /**
     * Return a normalized version of a float (and optionally a unit)
     *
     * @param string $string string to be normalized
     * @param string $unit   will contain the unit text
     * @return float The normalized value
     */
    protected function normalizeFloat(string $string, string &$unit): float
    {
        static $si_scale = [
            'T' => 12,
            'G' =>  9,
            'M' =>  6,
            'k' =>  3,
            'h' =>  2,
            'd' => -1,
            'c' => -2,
            'm' => -3,
            'µ' => -6,
            'μ' => -6,
            'n' => -9,
            'p' => -12
        ];

        // normalize representation
        $string = $this->normalizeText($string, false);
        $string = str_replace('*10^', 'e', $string);
        $string = preg_replace_callback('/(\d+)\/(\d+)/', function($m) { return $m[1] / $m[2]; }, $string);
        $string = strtr($string, ',', '.');

        // split into value and unit
        preg_match('/^([-+0-9.e]*)(.*)/', $string, $matches);
        $value = (float) $matches[1];
        $unit = trim($matches[2]);

        if ($unit) {
            $prefix = mb_substr($unit, 0, 1);
            $letter = mb_substr($unit, 1, 1);

            if (ctype_alpha($letter) && isset($si_scale[$prefix])) {
                $value *= pow(10, $si_scale[$prefix]);
                $unit = mb_substr($unit, 1);
            }
        }

        return $value;
    }

    /**
     * UTF-8 compatible version of standard PHP levenshtein function.
     */
    protected function levenshtein(string $string1, string $string2): int
    {
        $mb_str1 = preg_split('//u', $string1, null, PREG_SPLIT_NO_EMPTY);
        $mb_str2 = preg_split('//u', $string2, null, PREG_SPLIT_NO_EMPTY);

        $mb_len1 = count($mb_str1);
        $mb_len2 = count($mb_str2);

        $dist = [];
        for ($i = 0; $i <= $mb_len1; ++$i) {
            $dist[$i][0] = $i;
        }
        for ($j = 0; $j <= $mb_len2; ++$j) {
            $dist[0][$j] = $j;
        }

        for ($i = 1; $i <= $mb_len1; $i++) {
            for ($j = 1; $j <= $mb_len2; $j++) {
                $dist[$i][$j] = min(
                    $dist[$i-1][$j] + 1,
                    $dist[$i][$j-1] + 1,
                    $dist[$i-1][$j-1] + ($mb_str1[$i-1] !== $mb_str2[$j-1] ? 1 : 0)
                );
            }
        }

        return $dist[$mb_len1][$mb_len2];
    }

    /**
     * Scan the given string or array (recursively) for referenced file URLs
     * and rewrite those links into URNs suitable for XML export.
     */
    protected function rewriteLinksForExport(mixed $data): mixed
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->rewriteLinksForExport($value);
            }
        } else if (is_string($data) && Studip\Markup::isHtml($data)) {
            $data = preg_replace_callback('/"\Khttps?:[^"]*/', function($match) {
                $url = html_entity_decode($match[0]);
                $url = preg_replace(
                    '%/download/(?:normal|force_download)/\d/(\w+)/.+%',
                    '/sendfile.php?file_id=$1',
                    $url
                );
                [$url, $query] = explode('?', $url);

                if (is_internal_url($url) && basename($url) === 'sendfile.php') {
                    parse_str($query, $query_params);
                    $file_id = $query_params['file_id'];
                    $file_ref = FileRef::find($file_id);

                    if ($file_ref && $this->folder->file_refs->find($file_id)) {
                        return 'urn:vips:file-ref:file-' . $file_ref->file_id;
                    }

                    if ($file_ref) {
                        $folder = $file_ref->folder->getTypedFolder();

                        if ($folder->isFileDownloadable($file_ref->id, $GLOBALS['user']->id)) {
                            if (!$this->folder->file_refs->find($file_id)) {
                                $file = $file_ref->file;
                                // $this->files->append($file);
                            }

                            return 'urn:vips:file-ref:file-' . $file_id->file_id;
                        }
                    }
                }

                return $match[0];
            }, $data);
        }

        return $data;
    }

    /**
     * Calculate the size parameter for a flexible input element.
     *
     * @param string $text contents of the input
     */
    public function flexibleInputSize(?string $text): string
    {
        return str_contains($text, "\n") || Studip\Markup::isHtml($text) ? 'large' : 'small';
    }

    /**
     * Calculate the optimal textarea height for text exercises.
     *
     * @param string $text contents of textarea
     * @return int height of textarea in lines
     */
    public function textareaSize(?string $text): int
    {
        return max(substr_count($text, "\n") + 3, 5);
    }
}
