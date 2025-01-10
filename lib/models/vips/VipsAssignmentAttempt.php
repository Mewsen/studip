<?php
/*
 * VipsAssignmentAttempt.php - Vips test attempt class for Stud.IP
 * Copyright (c) 2016  Elmar Ludwig
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
 * @property int $assignment_id database column
 * @property string $user_id database column
 * @property int|null $start database column
 * @property int|null $end database column
 * @property string $ip_address database column
 * @property JSONArrayObject|null $options database column
 * @property int|null $mkdate database column
 * @property int|null $chdate database column
 * @property VipsAssignment $assignment belongs_to VipsAssignment
 * @property User $user belongs_to User
 */
class VipsAssignmentAttempt extends SimpleORMap
{
    /**
     * Configure the database mapping.
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'etask_assignment_attempts';

        $config['serialized_fields']['options'] = JSONArrayObject::class;

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
     * Return a student's event log for the assignment as a data array.
     */
    public function getLogEntries(): array
    {
        $assignment = $this->assignment;
        $user_id    = $this->user_id;
        $end_time   = min($this->end, $assignment->end);

        $solutions  = VipsSolution::findBySQL('assignment_id = ? AND user_id = ?', [$assignment->id, $user_id]);

        foreach ($assignment->test->exercise_refs as $exercise_ref) {
            $position[$exercise_ref->task_id] = $exercise_ref->position;
        }

        $logs[] = [
            'label'      => _('Beginn der Klausur'),
            'time'       => $this->start,
            'ip_address' => $this->ip_address,
            'session_id' => $this->options['session_id'],
            'archived'   => false
        ];

        foreach ($solutions as $solution) {
            if ($solution->isSubmitted()) {
                $logs[] = [
                    'label'      => sprintf(_('Abgabe Aufgabe %d'), $position[$solution->task_id]),
                    'time'       => $solution->mkdate,
                    'ip_address' => $solution->ip_address,
                    'session_id' => $solution->options['session_id'],
                    'archived'   => $solution->isArchived(),
                ];
            }
        }

        if ($end_time && $end_time < date('Y-m-d H:i:s')) {
            $logs[] = [
                'label'      => _('Ende der Klausur'),
                'time'       => $end_time,
                'ip_address' => '',
                'session_id' => '',
                'archived'   => false
            ];
        }

        usort($logs, fn($a, $b) => $a['time'] <=> $b['time']);

        return $logs;
    }
}
