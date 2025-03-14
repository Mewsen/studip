<?php
/*
 * VipsBlock.php - Vips block class for Stud.IP
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
 * @property string $name database column
 * @property string $range_id database column
 * @property string|null $group_id database column
 * @property int $visible database column
 * @property float|null $weight database column
 * @property SimpleORMapCollection<VipsAssignment> $assignments has_many VipsAssignment
 * @property Course $course belongs_to Course
 * @property Statusgruppen|null $group belongs_to Statusgruppen
 */
class VipsBlock extends SimpleORMap
{
    /**
     * Configure the database mapping.
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'etask_blocks';

        $config['has_many']['assignments'] = [
            'class_name'        => VipsAssignment::class,
            'assoc_foreign_key' => 'block_id'
        ];

        $config['belongs_to']['course'] = [
            'class_name'  => Course::class,
            'foreign_key' => 'range_id'
        ];
        $config['belongs_to']['group'] = [
            'class_name'  => Statusgruppen::class,
            'foreign_key' => 'group_id'
        ];

        parent::configure($config);
    }

    /**
     * Delete entry from the database.
     */
    public function delete()
    {
        foreach ($this->assignments as $assignment) {
            $assignment->block_id = null;
            $assignment->store();
        }

        return parent::delete();
    }

    /**
     * Check if this block is visible to this user.
     */
    public function isVisible(string $user_id): bool
    {
        $visible = $this->visible;

        if ($visible && $this->group_id) {
            $visible = StatusgruppeUser::exists([$this->group_id, $user_id]);
        }

        return $visible;
    }

    /**
     * Get the first assignment attempt of the given user for this block.
     * Returns null if there is no assignment attempt for this user.
     *
     * @param string $user_id   user id
     */
    public function getAssignmentAttempt(string $user_id): ?VipsAssignmentAttempt
    {
        $assignment_ids = $this->assignments->pluck('id');

        return VipsAssignmentAttempt::findOneBySQL(
            'assignment_id IN (?) AND user_id = ? ORDER BY start', [$assignment_ids, $user_id]
        );
    }
}
