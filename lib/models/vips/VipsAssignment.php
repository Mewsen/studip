<?php
/*
 * VipsAssignment.php - Vips test class for Stud.IP
 * Copyright (c) 2014  Elmar Ludwig
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
 * @property int $test_id database column
 * @property string|null $range_type database column
 * @property string|null $range_id database column
 * @property string $type database column
 * @property int|null $start database column
 * @property int|null $end database column
 * @property int $active database column
 * @property float $weight database column
 * @property int|null $block_id database column
 * @property JSONArrayObject $options database column
 * @property int|null $mkdate database column
 * @property int|null $chdate database column
 * @property SimpleORMapCollection|VipsAssignmentAttempt[] $assignment_attempts has_many VipsAssignmentAttempt
 * @property SimpleORMapCollection|VipsSolution[] $solutions has_many VipsSolution
 * @property Course|null $course belongs_to Course
 * @property VipsBlock|null $block belongs_to VipsBlock
 * @property VipsTest $test belongs_to VipsTest
 */
class VipsAssignment extends SimpleORMap
{
    public const RELEASE_STATUS_NONE = 0;
    public const RELEASE_STATUS_POINTS = 1;
    public const RELEASE_STATUS_COMMENTS = 2;
    public const RELEASE_STATUS_CORRECTIONS = 3;
    public const RELEASE_STATUS_SAMPLE_SOLUTIONS = 4;

    public const SCORING_DEFAULT = 0;
    public const SCORING_NEGATIVE_POINTS = 1;
    public const SCORING_ALL_OR_NOTHING = 2;

    /**
     * Configure the database mapping.
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'etask_assignments';

        $config['serialized_fields']['options'] = JSONArrayObject::class;

        $config['has_many']['assignment_attempts'] = [
            'class_name'        => VipsAssignmentAttempt::class,
            'assoc_foreign_key' => 'assignment_id'
        ];
        $config['has_many']['solutions'] = [
            'class_name'        => VipsSolution::class,
            'assoc_foreign_key' => 'assignment_id'
        ];

        $config['belongs_to']['course'] = [
            'class_name'  => Course::class,
            'foreign_key' => 'range_id'
        ];
        $config['belongs_to']['block'] = [
            'class_name'  => VipsBlock::class,
            'foreign_key' => 'block_id'
        ];
        $config['belongs_to']['test'] = [
            'class_name'  => VipsTest::class,
            'foreign_key' => 'test_id'
        ];

        parent::configure($config);
    }

    /**
     * Initialize a new instance of this class.
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        if (is_null($this->options)) {
            $this->options = [];
        }
    }

    /**
     * Delete entry from the database.
     */
    public function delete()
    {
        $gradebook_id = $this->options['gradebook_id'];

        if ($gradebook_id) {
            Grading\Definition::deleteBySQL('id = ?', [$gradebook_id]);
        }

        VipsAssignmentAttempt::deleteBySQL('assignment_id = ?', [$this->id]);

        $ref_count = self::countBySql('test_id = ?', [$this->test_id]);

        if ($ref_count === 1) {
            $this->test->delete();
        }

        return parent::delete();
    }

    /**
     * Find all assignments for a given range_id.
     *
     * @return VipsAssignment[]
     */
    public static function findByRangeId($range_id)
    {
        return VipsAssignment::findBySQL(
            'range_id = ? AND type IN (?) ORDER BY start',
            [$range_id, ['exam', 'practice', 'selftest']]
        );
    }

    public static function importText(
        string $title,
        string $string,
        string $user_id,
        string $range_id,
        string $range_type = 'course'
    ): VipsAssignment {
        $duration = 7 * 24 * 60 * 60;  // one week

        $data_test = [
            'title'       => $title !== '' ? $title : _('Aufgabenblatt'),
            'description' => '',
            'user_id'     => $user_id
        ];
        $data = [
            'type'       => 'practice',
            'range_id'   => $range_id,
            'range_type' => $range_type,
            'start'      => strtotime(date('Y-m-d H:00:00')),
            'end'        => strtotime(date('Y-m-d H:00:00', time() + $duration))
        ];

        // remove comments
        $string = preg_replace('/^#.*/m', '', $string);

        // split into exercises
        $segments = preg_split('/^Name:/m', $string);
        array_shift($segments);

        $test_obj = VipsTest::create($data_test);

        $result = self::build($data);
        $result->test = $test_obj;
        $result->store();

        foreach ($segments as $segment) {
            try {
                $new_exercise = Exercise::importText($segment);
                $new_exercise->user_id = $user_id;
                $new_exercise->store();
                $test_obj->addExercise($new_exercise);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (isset($errors)) {
            PageLayout::postError(_('Während des Imports sind folgende Fehler aufgetreten:'), $errors);
        }

        return $result;
    }

    public static function importXML(
        string $string,
        string $user_id,
        string $range_id,
        string $range_type = 'course'
    ): VipsAssignment {
        // default options
        $options = [
            'evaluation_mode' => 0,
            'released'        => 0
        ];

        $duration = 7 * 24 * 60 * 60;  // one week

        $data_test = [
            'title'       => _('Aufgabenblatt'),
            'description' => '',
            'user_id'     => $user_id
        ];
        $data = [
            'type'        => 'practice',
            'range_id'    => $range_id,
            'range_type'  => $range_type,
            'start'       => strtotime(date('Y-m-d H:00:00')),
            'end'         => strtotime(date('Y-m-d H:00:00', time() + $duration)),
            'options'     => $options
        ];

        $test = new SimpleXMLElement($string, LIBXML_COMPACT | LIBXML_NOCDATA);
        $data['type'] = (string) $test['type'];

        if (trim($test->title) !== '') {
            $data_test['title'] = trim($test->title);
        }
        if ($test->description) {
            $data_test['description'] = Studip\Markup::purifyHtml(trim($test->description));
        }
        if ($test->notes) {
            $data['options']['notes'] = trim($test->notes);
        }

        if ($test->limit['access-code']) {
            $data['options']['access_code'] = (string) $test->limit['access-code'];
        }
        if ($test->limit['ip-ranges']) {
            $data['options']['ip_range'] = (string) $test->limit['ip-ranges'];
        }
        if ($test->limit['resets']) {
            $data['options']['resets'] = (int) $test->limit['resets'];
        }
        if ($test->limit['tries']) {
            $data['options']['max_tries'] = (int) $test->limit['tries'];
        }

        if ($test->option['scoring-mode'] == 'negative_points') {
            $data['options']['evaluation_mode'] = self::SCORING_NEGATIVE_POINTS;
        } else if ($test->option['scoring-mode'] == 'all_or_nothing') {
            $data['options']['evaluation_mode'] = self::SCORING_ALL_OR_NOTHING;
        }
        if ($test->option['shuffle-answers'] == 'true') {
            $data['options']['shuffle_answers'] = 1;
        }
        if ($test->option['shuffle-exercises'] == 'true') {
            $data['options']['shuffle_exercises'] = 1;
        }

        if ($test['start']) {
            $data['start'] = strtotime($test['start']);
        }
        if ($test['end']) {
            $data['end'] = strtotime($test['end']);
        } else if ($data['type'] === 'selftest') {
            $data['end'] = null;
        }
        if ($test['duration']) {
            $data['options']['duration'] = (int) $test['duration'];
        }
        if ($test['block'] && $range_type === 'course') {
            $block = VipsBlock::findOneBySQL('name = ? AND range_id = ?', [$test['block'], $range_id]);

            if (!$block) {
                $block = VipsBlock::create(['name' => $test['block'], 'range_id' => $range_id]);
            }

            $data['block_id'] = $block->id;
        }

        if ($test->{'feedback-items'}) {
            foreach ($test->{'feedback-items'}->feedback as $feedback) {
                $threshold = (int) ($feedback['score'] * 100);
                $data['options']['feedback'][$threshold] = Studip\Markup::purifyHtml(trim($feedback));
            }

            krsort($data['options']['feedback']);
        }

        $test_obj = VipsTest::create($data_test);

        $result = self::build($data);
        $result->test = $test_obj;
        $result->store();

        if ($test->files) {
            foreach ($test->files->file as $file) {
                $file_id = (string) $file['id'];
                $content = base64_decode((string) $file);

                $test->registerXPathNamespace('vips', 'urn:vips:test:v1.0');
                $file_refs = $test->xpath('vips:exercises/*/vips:file-refs/*[@ref="' . $file_id . '"]');

                if ($file_refs && $content !== false) {
                    if (strlen($file_id) > 5 && str_starts_with($file_id, 'file-')) {
                        $vips_file = File::find(substr($file_id, 5));

                        // try to avoid reupload of identical files
                        if ($vips_file && sha1_file($vips_file->getPath()) === sha1($content)) {
                            $files[$file_id] = $vips_file;
                            continue;
                        }
                    }

                    $file = File::create([
                        'user_id'   => $user_id,
                        'mime_type' => get_mime_type($file['name']),
                        'name'      => basename($file['name']),
                        'size'      => strlen($content)
                    ]);

                    file_put_contents($file->getPath(), $content);
                }
            }

            if (isset($files)) {
                $mapped = preg_replace_callback(
                    '/\burn:vips:file-ref:([A-Za-z_][\w.-]*)/',
                    function($match) use ($files) {
                        $file = $files[$match[1]];

                        if ($file) {
                            return htmlReady($file->getDownloadURL());
                        } else {
                            return $match[0];
                        }
                    }, $string
                );
                $test = new SimpleXMLElement($mapped, LIBXML_COMPACT | LIBXML_NOCDATA);
            }
        }

        foreach ($test->exercises->exercise as $exercise) {
            try {
                $new_exercise = Exercise::importXML($exercise);
                $new_exercise->user_id = $user_id;
                $new_exercise->store();
                $exercise_ref = $test_obj->addExercise($new_exercise);

                if ($exercise['points']) {
                    $exercise_ref->points = (float) $exercise['points'];
                    $exercise_ref->store();
                }

                if ($exercise->{'file-refs'}) {
                    $folder = Folder::findTopFolder($new_exercise->id, 'ExerciseFolder', 'task');

                    foreach ($exercise->{'file-refs'}->{'file-ref'} as $file_ref) {
                        $file = $files[(string) $file_ref['ref']];

                        if ($file) {
                            FileRef::create([
                                'file_id'   => $file->id,
                                'folder_id' => $folder->id,
                                'object_id' => $new_exercise->id,
                                'user_id'   => $user_id,
                                'name'      => $file->name
                            ]);
                        }
                    }
                }
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (isset($errors)) {
            PageLayout::postError(_('Während des Imports sind folgende Fehler aufgetreten:'), $errors);
        }

        return $result;
    }

    /**
     * Get the name of this assignment type.
     */
    public function getTypeName(): string
    {
        $assignment_types = self::getAssignmentTypes();

        return $assignment_types[$this->type]['name'];
    }

    /**
     * Get the icon of this assignment type.
     */
    public function getTypeIcon(string $role = Icon::DEFAULT_ROLE): Icon
    {
        $assignment_types = self::getAssignmentTypes();

        return Icon::create(
            $assignment_types[$this->type]['icon'],
            $role,
            ['aria-hidden' => 'true', 'title' => $assignment_types[$this->type]['name']]
        );
    }

    /**
     * Get the list of supported assignment types.
     */
    public static function getAssignmentTypes(): array
    {
        return [
            'practice' => ['name' => _('Übung'),      'icon' => 'file'],
            'selftest' => ['name' => _('Selbsttest'), 'icon' => 'check-circle'],
            'exam'     => ['name' => _('Klausur'),    'icon' => 'doctoral_cap']
        ];
    }

    /**
     * Check if this assignment is locked for editing.
     */
    public function isLocked(): bool
    {
        return $this->type === 'exam' && $this->countAssignmentAttempts() > 0;
    }

    /**
     * Check if this assignment is visible to this user.
     */
    public function isVisible(string $user_id): bool
    {
        return $this->block_id ? $this->block->isVisible($user_id) : true;
    }

    /**
     * Check if this assignment has been started.
     */
    public function isStarted(): bool
    {
        $now = time();

        return $now >= $this->start;
    }

    /**
     * Check if this assignment is currently running.
     *
     * @param string|null $user_id check end time for this user id (optional)
     */
    public function isRunning(?string $user_id = null): bool
    {
        $now = time();
        $end = $user_id ? $this->getUserEndTime($user_id) : $this->end;

        return $now >= $this->start && ($end === null || $now <= $end);
    }

    /**
     * Check if this assignment is already finished.
     *
     * @param string|null $user_id check end time for this user id (optional)
     */
    public function isFinished(?string $user_id = null): bool
    {
        $now = time();
        $end = $user_id ? $this->getUserEndTime($user_id) : $this->end;

        return $end && $now > $end;
    }

    /**
     * Check if this assignment has no end date.
     */
    public function isUnlimited(): bool
    {
        return $this->type === 'selftest' && $this->end === null;
    }

    /**
     * Check if this assignment may use self assessment features.
     */
    public function isSelfAssessment(): bool
    {
        return $this->type === 'selftest' || $this->options['self_assessment'];
    }

    /**
     * Check if a user may reset and restart this assignment.
     */
    public function isResetAllowed(): bool
    {
        return $this->isSelfAssessment() && $this->options['resets'] !== 0;
    }

    /**
     * Check if this assignment presents shuffled exercises.
     */
    public function isExerciseShuffled(): bool
    {
        return $this->type === 'exam' && $this->options['shuffle_exercises'];
    }

    /**
     * Check if this assignment presents shuffled answers.
     */
    public function isShuffled(): bool
    {
        return $this->type === 'exam' && $this->options['shuffle_answers'] !== 0;
    }

    /**
     * Check if this assignment is using group solutions.
     */
    public function hasGroupSolutions(): bool
    {
        return $this->type === 'practice' && $this->options['use_groups'] !== 0;
    }

    /**
     * Get the number of tries allowed for exercises on this assignment.
     */
    public function getMaxTries(): int
    {
        if ($this->type === 'selftest') {
            return $this->options['max_tries'] ?? 3;
        }

        return 0;
    }

    /**
     * Check whether the given exercise is part of this assignment.
     *
     * @param int $exercise_id exercise id
     */
    public function hasExercise(int $exercise_id): bool
    {
        return VipsExerciseRef::exists([$this->test_id, $exercise_id]);
    }

    /**
     * Return array of exercise refs in the test of this assignment.
     */
    public function getExerciseRefs(?string $user_id): array
    {
        $result = $this->test->exercise_refs->getArrayCopy();

        if ($this->isExerciseShuffled() && $user_id) {
            srand(crc32($this->id . ':' . $user_id));
            shuffle($result);
            srand();
        }

        return $result;
    }

    /**
     * Export this assignment to XML format. Returns the XML string.
     */
    public function exportXML(): string
    {
        $files = [];

        foreach ($this->test->exercise_refs as $exercise_ref) {
            $exercise = $exercise_ref->exercise;
            $exercise->includeFilesForExport();

            if ($exercise->folder) {
                foreach ($exercise->folder->file_refs as $file_ref) {
                    $files[$file_ref->file_id] = $file_ref->file;
                }
            }
        }

        $template = VipsModule::$template_factory->open('sheets/export_assignment');
        $template->assignment = $this;
        $template->files = $files;

        // delete all characters outside the valid character range for XML
        // documents (#x9 | #xA | #xD | [#x20-#xD7FF] | [#xE000-#xFFFD]).
        return preg_replace("/[^\t\n\r -\xFF]/", '', $template->render());
    }

    /**
     * Check whether this assignment is editable by the given user.
     *
     * @param string|null $user_id user to check (defaults to current user)
     */
    public function checkEditPermission(?string $user_id = null): bool
    {
        if ($this->range_type === 'user') {
            return $this->range_id === ($user_id ?: $GLOBALS['user']->id);
        }

        return $GLOBALS['perm']->have_studip_perm('tutor', $this->range_id, $user_id);
    }

    /**
     * Check whether this assignment is viewable by the given user.
     *
     * @param string|null $user_id user to check (defaults to current user)
     */
    public function checkViewPermission(?string $user_id = null): bool
    {
        if ($this->range_type === 'user') {
            return $this->range_id === ($user_id ?: $GLOBALS['user']->id);
        }

        return $GLOBALS['perm']->have_studip_perm('autor', $this->range_id, $user_id);
    }

    /**
     * Check whether this assignment is accessible to a student. This is just
     * a shortcut for checking: running, active, ip address and access code.
     *
     * @param string $user_id   check end time for this user id (optional)
     */
    public function checkAccess($user_id = null): bool
    {
        return $this->isRunning($user_id)
            && $this->active && $this->checkAccessCode()
            && $this->checkIPAccess($_SERVER['REMOTE_ADDR']);
    }

    /**
     * Check whether the access code provided for this assignment is valid.
     * If $access_code is null, the code stored in the user session is used.
     *
     * @param string|null $access_code access code (optional)
     */
    public function checkAccessCode(?string $access_code = null): bool
    {
        if (isset($access_code)) {
            $_SESSION['vips_access_' . $this->id] = $access_code;
        } else if (isset($_SESSION['vips_access_' . $this->id])) {
            $access_code = $_SESSION['vips_access_' . $this->id];
        } else {
            $access_code = null;
        }

        return in_array($this->options['access_code'], [null, $access_code], true);
    }

    /**
     * Check whether the given IP address listed among the IP addresses given
     * by the lecturer for this exam (if applicable).
     *
     * @param string $ip_addr IPv4 or IPv6 address
     */
    public function checkIPAccess(string $ip_addr): bool
    {
        // not an exam: user has access.
        if ($this->type !== 'exam') {
            return true;
        }

        $ip_addr = inet_pton($ip_addr);
        $ip_ranges = $this->options['ip_range'];
        $exam_rooms = Config::get()->VIPS_EXAM_ROOMS;

        // expand exam room names
        if ($exam_rooms) {
            $ip_ranges = preg_replace_callback('/#([^ ,]+)/',
                function($match) use ($exam_rooms) {
                    return $exam_rooms[$match[1]];
                }, $ip_ranges);
        }

        // Explode space separated list into an array and check the resulting single IPs
        $ip_ranges = preg_split('/[ ,]+/', $ip_ranges, -1, PREG_SPLIT_NO_EMPTY);

        // No IP given: user has access.
        if (count($ip_ranges) == 0) {
            return true;
        }

        // One or more IPs are given and user IP matches at least one: user has access.
        foreach ($ip_ranges as $ip_range) {
            if (str_contains($ip_range, '/')) {
                [$ip_range, $bits] = explode('/', $ip_range);
                $ip_range = inet_pton($ip_range) ?: '';
                $mask = str_repeat(chr(0), strlen($ip_range));

                for ($i = 0; $i < strlen($mask); ++$i) {
                    if ($bits >= 8) {
                        $bits -= 8;
                    } else {
                        $mask[$i] = chr((1 << 8 - $bits) - 1);
                        $bits = 0;
                    }
                }

                $ip_start = $ip_range & ~$mask;
                $ip_end = $ip_range | $mask;
            } else {
                if (str_contains($ip_range, '-')) {
                    [$ip_start, $ip_end] = explode('-', $ip_range);
                } else {
                    $ip_start = $ip_end = $ip_range;
                }

                if (!str_contains($ip_range, ':')) {
                    $ip_start = implode('.', array_pad(explode('.', $ip_start), 4, 0));
                    $ip_end = implode('.', array_pad(explode('.', $ip_end), 4, 255));
                }

                $ip_start = inet_pton($ip_start);
                $ip_end = inet_pton($ip_end);
            }

            if (strcmp($ip_start, $ip_addr) <= 0 && strcmp($ip_addr, $ip_end) <= 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the release status of this assignment for the given user.
     *
     * Valid values are:
     *  - 0 = not released
     *  - 1 = points
     *  - 2 = comments
     *  - 3 = corrections
     *  - 4 = sample solutions
     *
     * See the according constants of this class.
     */
    public function releaseStatus(string $user_id): int
    {
        if ($this->isFinished() || $this->isSelfAssessment() && $this->isFinished($user_id)) {
            if ($this->type === 'exam') {
                if ($this->getAssignmentAttempt($user_id)) {
                    return $this->options['released'] ?? self::RELEASE_STATUS_NONE;
                }
            } else {
                if ($this->options['released'] > 0) {
                    return $this->options['released'];
                }
            }
        }

        return self::RELEASE_STATUS_NONE;
    }

    /**
     * Count the number of assignment attempts for this assignment.
     */
    public function countAssignmentAttempts(): int
    {
        return VipsAssignmentAttempt::countBySql('assignment_id = ?', [$this->id]);
    }

    /**
     * Get the assignment attempt of the given user for this assignment.
     * Returns null if there is no assignment attempt for this user.
     *
     * @param string $user_id user id
     */
    public function getAssignmentAttempt(string $user_id): ?VipsAssignmentAttempt
    {
        return VipsAssignmentAttempt::findOneBySQL('assignment_id = ? AND user_id = ?', [$this->id, $user_id]);
    }

    /**
     * Record an assignment attempt for the given user for this assignment.
     */
    public function recordAssignmentAttempt(string $user_id): void
    {
        if (!$this->getAssignmentAttempt($user_id)) {
            if ($this->type === 'exam') {
                $end = time() + $this->options['duration'] * 60;
                $ip_address = $_SERVER['REMOTE_ADDR'];
                $options = ['session_id' => session_id()];
            } else {
                $end = null;
                $ip_address = '';
                $options = null;
            }

            VipsAssignmentAttempt::create([
                'assignment_id' => $this->id,
                'user_id'       => $user_id,
                'start'         => time(),
                'end'           => $end,
                'ip_address'    => $ip_address,
                'options'       => $options
            ]);
        }
    }

    /**
     * Finish an assignment attempt for the given user for this assignment.
     */
    public function finishAssignmentAttempt(string $user_id): ?VipsAssignmentAttempt
    {
        $assignment_attempt = $this->getAssignmentAttempt($user_id);
        $now = time();

        if ($assignment_attempt) {
            if ($assignment_attempt->end === null || $assignment_attempt->end > $now) {
                $assignment_attempt->end = $now;
                $assignment_attempt->store();
            }
        }

        return $assignment_attempt;
    }

    /**
     * Get the individual end time of the given user for this assignment.
     */
    public function getUserEndTime(string $user_id): ?int
    {
        if ($this->type === 'practice') {
            return $this->end;
        }

        $assignment_attempt = $this->getAssignmentAttempt($user_id);

        if ($assignment_attempt) {
            $start = $assignment_attempt->start;
        } else {
            $start = time();
        }

        if ($assignment_attempt && $assignment_attempt->end) {
            return min($assignment_attempt->end, $this->end ?: $assignment_attempt->end);
        } else if ($this->type === 'exam') {
            return min($start + $this->options['duration'] * 60, $this->end);
        } else {
            return $this->end;
        }
    }

    /**
     * Get all members that were assigned to a particular group for
     * this assignment.
     *
     * @param VipsGroup $group   The group object
     * @return VipsGroupMember[]
     */
    public function getGroupMembers($group): array
    {
        return VipsGroupMember::findBySQL(
            'group_id = ? AND start < ? AND (end > ? OR end IS NULL)',
            [$group->id, $this->end, $this->end]
        );
    }

    /**
     * Get the group the user was assigned to for this assignment.
     * Returns null if there is no group assignment for this user.
     */
    public function getUserGroup(string $user_id): ?VipsGroup
    {
        if (!$this->hasGroupSolutions()) {
            return null;
        }

        return VipsGroup::findOneBySQL(
            'JOIN etask_group_members ON group_id = statusgruppe_id
             WHERE range_id  = ?
               AND user_id   = ?
               AND start     < ?
               AND (end      > ? OR end IS NULL)',
            [$this->range_id, $user_id, $this->end, $this->end]
        );
    }

    /**
     * Store a solution related to this assignment into the database.
     *
     * @param VipsSolution $solution The solution object
     */
    public function storeSolution(VipsSolution $solution): bool|int
    {
        $solution->assignment = $this;

        // store some client info for exams
        if ($this->type === 'exam') {
            $solution->ip_address = $_SERVER['REMOTE_ADDR'];
            $solution->options['session_id'] = session_id();
        }

        // in selftests, autocorrect solution
        if ($this->isSelfAssessment()) {
            $this->correctSolution($solution);
        }

        // insert new solution into etask_responses
        return $solution->store();
    }

    /**
     * Correct a solution and store the points for the solution in the object.
     *
     * @param VipsSolution $solution  The solution object
     * @param bool         $corrected mark solution as corrected
     */
    public function correctSolution(VipsSolution $solution, bool $corrected = false): void
    {
        $exercise = $solution->exercise;
        $exercise_ref = $this->test->getExerciseRef($exercise->id);
        $max_points = (float) $exercise_ref->points;

        // always set corrected to true for selftest exercises
        $selftest   = $this->type === 'selftest';
        $evaluation = $exercise->evaluate($solution);
        $eval_safe  = $selftest ? $evaluation['safe'] !== null : $evaluation['safe'];

        $reached_points = round($evaluation['percent'] * $max_points * 2) / 2;
        $corrected      = (int) ($corrected || $eval_safe);

        // insert solution points
        $solution->state = $corrected;
        $solution->points = $reached_points;
        $solution->chdate = time();

        if ($selftest && $evaluation['percent'] != 1 && isset($exercise->options['feedback'])) {
            $solution->feedback = $exercise->options['feedback'];
        }
    }

    /**
     * Restores an archived solution as the current solution.
     *
     * @param VipsSolution $solution The solution object
     */
    public function restoreSolution(VipsSolution $solution): void
    {
        if ($solution->isArchived() && $solution->assignment_id == $this->id) {
            $new_solution = VipsSolution::build($solution);
            $new_solution->id = 0;

            if ($solution->folder) {
                $new_solution->store();
                $folder = Folder::findTopFolder($new_solution->id, 'ResponseFolder', 'response');

                foreach ($solution->folder->file_refs as $file_ref) {
                    FileManager::copyFile($file_ref->getFileType(), $folder->getTypedFolder(), $file_ref->user);
                }
            }

            $this->storeSolution($new_solution);
        }
    }

    /**
     * Fetch archived solutions related to this assignment from the database.
     * Returns empty list if there are no archived solutions for this exercise.
     *
     * @return VipsSolution[]
     */
    public function getArchivedGroupSolutions(string $group_id, int $exercise_id): array
    {
        return VipsSolution::findBySQL(
            'JOIN etask_group_members USING(user_id)
             WHERE task_id   = ?
               AND assignment_id = ?
               AND group_id      = ?
               AND start         < ?
               AND (end          > ? OR end IS NULL)
             ORDER BY mkdate DESC',
            [$exercise_id, $this->id, $group_id, $this->end, $this->end]
        );
    }

    /**
     * Fetch archived solutions related to this assignment from the database.
     * NOTE: This method will NOT check the group solutions, if applicable.
     * Returns empty list if there are no archived solutions for this exercise.
     *
     * @return VipsSolution[]
     */
    public function getArchivedUserSolutions(string $user_id, int $exercise_id): array
    {
        return VipsSolution::findBySQL(
            'task_id = ? AND assignment_id = ? AND user_id = ? ORDER BY mkdate DESC',
            [$exercise_id, $this->id, $user_id]
        );
    }

    /**
     * Fetch archived solutions related to this assignment from the database.
     * Returns empty list if there are no archived solutions for this exercise.
     *
     * @return VipsSolution[]
     */
    public function getArchivedSolutions(string $user_id, int $exercise_id): array
    {
        $group = $this->getUserGroup($user_id);

        if ($group) {
            return $this->getArchivedGroupSolutions($group->id, $exercise_id);
        }

        return $this->getArchivedUserSolutions($user_id, $exercise_id);
    }

    /**
     * Fetch a solution related to this assignment from the database.
     * Returns null if there is no solution for this exercise yet.
     */
    public function getGroupSolution(string $group_id, int $exercise_id): ?VipsSolution
    {
        return VipsSolution::findOneBySQL(
            'JOIN etask_group_members USING(user_id)
             WHERE task_id   = ?
               AND assignment_id = ?
               AND group_id      = ?
               AND start         < ?
               AND (end          > ? OR end IS NULL)
             ORDER BY mkdate DESC',
            [$exercise_id, $this->id, $group_id, $this->end, $this->end]
        );
    }

    /**
     * Fetch a solution related to this assignment from the database.
     * NOTE: This method will NOT check the group solution, if applicable.
     * Returns null if there is no solution for this exercise yet.
     */
    public function getUserSolution(string $user_id, int $exercise_id): ?VipsSolution
    {
        return VipsSolution::findOneBySQL(
            'task_id = ? AND assignment_id = ? AND user_id = ? ORDER BY mkdate DESC',
            [$exercise_id, $this->id, $user_id]
        );
    }

    /**
     * Fetch a solution related to this assignment from the database.
     * Returns null if there is no solution for this exercise yet.
     */
    public function getSolution(string $user_id, int $exercise_id): ?VipsSolution
    {
        $group = $this->getUserGroup($user_id);

        if ($group) {
            return $this->getGroupSolution($group->id, $exercise_id);
        }

        return $this->getUserSolution($user_id, $exercise_id);
    }

    /**
     * Delete all solutions of the given user for a single exercise of
     * this test from the DB.
     */
    public function deleteSolution(string $user_id, int $exercise_id): void
    {
        $sql = 'task_id = ? AND assignment_id = ? AND user_id = ?';

        if ($this->isSelfAssessment()) {
            // delete in etask_responses
            VipsSolution::deleteBySQL($sql, [$exercise_id, $this->id, $user_id]);
        }

        // update gradebook if necessary
        $this->updateGradebookEntries($user_id);
    }

    /**
     * Delete all solutions of the given user for this test from the DB.
     */
    public function deleteSolutions(string $user_id): void
    {
        $sql = 'assignment_id = ? AND user_id = ?';

        if ($this->isSelfAssessment()) {
            // delete in etask_responses
            VipsSolution::deleteBySQL($sql, [$this->id, $user_id]);
        }

        // delete start times
        VipsAssignmentAttempt::deleteBySQL($sql, [$this->id, $user_id]);

        // update gradebook if necessary
        $this->updateGradebookEntries($user_id);
    }

    /**
     * Delete all solutions of all users for this test from the DB.
     */
    public function deleteAllSolutions(): void
    {
        $sql = 'assignment_id = ?';

        if ($this->isSelfAssessment()) {
            // delete in etask_responses
            VipsSolution::deleteBySQL($sql, [$this->id]);
        }

        // delete start times
        VipsAssignmentAttempt::deleteBySQL($sql, [$this->id]);

        // update gradebook if necessary
        $this->updateGradebookEntries();
    }

    /**
     * Count the number of solutions of the given user for this test.
     */
    public function countSolutions(string $user_id): int
    {
        $solutions = 0;

        foreach ($this->test->exercise_refs as $exercise_ref) {
            if ($this->getSolution($user_id, $exercise_ref->task_id)) {
                ++$solutions;
            }
        }

        return $solutions;
    }

    /**
     * Return the points a user has reached in all exercises in this assignment.
     */
    public function getUserPoints(string $user_id): float|int
    {
        $group = $this->getUserGroup($user_id);

        if ($group) {
            $user_ids = array_column($this->getGroupMembers($group), 'user_id');
        } else {
            $user_ids = [$user_id];
        }

        $solutions = $this->solutions->findBy('user_id', $user_ids)->orderBy('mkdate');
        $points = [];

        foreach ($solutions as $solution) {
            $points[$solution->task_id] = (float) $solution->points;
        }

        return max(array_sum($points), 0);
    }

    /**
     * Return the progress a user has achieved on this assignment (range 0..1).
     */
    public function getUserProgress(string $user_id): float|int
    {
        $group = $this->getUserGroup($user_id);
        $max_points = 0;
        $progress = 0;

        foreach ($this->test->exercise_refs as $exercise_ref) {
            $max_points += $exercise_ref->points;

            if ($group) {
                $solution = $this->getGroupSolution($group->id, $exercise_ref->task_id);
            } else {
                $solution = $this->getUserSolution($user_id, $exercise_ref->task_id);
            }

            if ($solution) {
                $progress += $exercise_ref->points;
            }
        }

        return $max_points ? $progress / $max_points : 0;
    }

    /**
     * Return the individual feedback text for the given user in this assignment.
     */
    public function getUserFeedback(string $user_id): ?string
    {
        if (isset($this->options['feedback'])) {
            $user_points = $this->getUserPoints($user_id);
            $max_points = $this->test->getTotalPoints();
            $percent = $user_points / $max_points * 100;

            foreach ($this->options['feedback'] as $threshold => $feedback) {
                if ($percent >= $threshold) {
                    return $feedback;
                }
            }
        }

        return null;
    }

    /**
     * Copy this assignment into the given course. Returns the new assignment.
     */
    public function copyIntoCourse(string $course_id, string $range_type = 'course'): ?VipsAssignment
    {
        // determine title of new assignment
        if ($this->range_id === $course_id) {
            $title = sprintf(_('Kopie von %s'), $this->test->title);
        } else {
            $title = $this->test->title;
        }

        // reset released option for new assignment
        $options = $this->options;
        unset($options['released']);
        unset($options['stopdate']);
        unset($options['gradebook_id']);

        $new_test = VipsTest::create([
            'title'       => $title,
            'description' => $this->test->description,
            'user_id'     => $GLOBALS['user']->id
        ]);

        $new_assignment = VipsAssignment::create([
            'test_id'    => $new_test->id,
            'range_id'   => $course_id,
            'range_type' => $range_type,
            'type'       => $this->type,
            'start'      => $this->start,
            'end'        => $this->end,
            'options'    => $options
        ]);

        foreach ($this->test->exercise_refs as $exercise_ref) {
            $exercise_ref->copyIntoTest($new_test->id, $exercise_ref->position);
        }

        return $new_assignment;
    }

    /**
     * Move this assignment into the given course.
     */
    public function moveIntoCourse(string $course_id, string $range_type = 'course'): void
    {
        if ($this->range_id !== $course_id) {
            $this->range_id = $course_id;
            $this->range_type = $range_type;
            $this->block_id = null;
            $this->removeFromGradebook();
            $this->store();
        }
    }

    /**
     * Insert this assignment into the gradebook of its course.
     *
     * @param string $title  gradebook title
     * @param float  $weight gradebook weight
     */
    public function insertIntoGradebook(string $title, float $weight = 1): void
    {
        $gradebook_id = $this->options['gradebook_id'];

        if (!$gradebook_id) {
            $definition = Grading\Definition::create([
                'course_id' => $this->range_id,
                'item'      => $this->id,
                'name'      => $title,
                'tool'      => _('Aufgaben'),
                'category'  => $this->getTypeName(),
                'position'  => $this->start,
                'weight'    => $weight
            ]);

            $this->options['gradebook_id'] = $definition->id;
            $this->store();
        }
    }

    /**
     * Remove this assignment from the gradebook of its course.
     */
    public function removeFromGradebook(): void
    {
        $gradebook_id = $this->options['gradebook_id'];

        if ($gradebook_id) {
            Grading\Definition::find($gradebook_id)->delete();

            unset($this->options['gradebook_id']);
            $this->store();
        }
    }

    /**
     * Update some or all gradebook entries of this assignment. If the
     * user_id is specified, only update entries related to this user.
     *
     * @param string|null $user_id user id
     */
    public function updateGradebookEntries(?string $user_id = null): void
    {
        $gradebook_id = $this->options['gradebook_id'];

        if ($gradebook_id) {
            $max_points = $this->test->getTotalPoints() ?: 1;

            if ($user_id) {
                $group = $this->getUserGroup($user_id);
            }

            if ($group) {
                $members = $this->getGroupMembers($group);
            } else if ($user_id) {
                $members = [(object) compact('user_id')];
            } else {
                $members = $this->course->members->findBy('status', 'autor');
            }

            foreach ($members as $member) {
                $reached_points = $this->getUserPoints($member->user_id);
                $entry = new Grading\Instance([$gradebook_id, $member->user_id]);

                if ($reached_points) {
                    $entry->rawgrade = $reached_points / $max_points;
                    $entry->store();
                } else {
                    $entry->delete();
                }
            }
        }
    }
}
