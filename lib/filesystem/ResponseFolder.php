<?php
/*
 * ExerciseFolder.php - Vips response folder class for Stud.IP
 * Copyright (c) 2024  Elmar Ludwig
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

class ResponseFolder extends StandardFolder
{
    public static function availableInRange(SimpleORMap|string $range_id_or_object, string $user_id): bool
    {
        return false;
    }

    public function isReadable(string $user_id): bool
    {
        $solution = VipsSolution::find($this->range_id);
        $assignment = $solution->assignment;

        if (!$assignment->checkViewPermission()) {
            return false;
        }

        if ($assignment->checkEditPermission() || $solution->user_id === $user_id) {
            return true;
        }

        $group = $assignment->getUserGroup($solution->user_id);
        $group2 = $assignment->getUserGroup($user_id);

        return isset($group, $group2)
            && $group->id === $group2->id;
    }

    public function isWritable(string $user_id): bool
    {
        $solution = VipsSolution::find($this->range_id);
        $assignment = $solution->assignment;

        return $assignment->checkEditPermission();
    }

    public function isEditable(string $user_id): bool
    {
        return false;
    }

    public function isSubfolderAllowed(string $user_id): bool
    {
        return false;
    }

    public function isFileDownloadable(FileRef $file_ref, string $user_id): bool
    {
        return $this->isReadable($user_id);
    }

    public function isFileEditable(FileRef $file_ref, string $user_id): bool
    {
        return $this->isWritable($user_id);
    }

    public function isFileWritable(FileRef $file_ref, string $user_id): bool
    {
        return $this->isWritable($user_id);
    }
}
