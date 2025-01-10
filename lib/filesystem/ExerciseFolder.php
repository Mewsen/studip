<?php
/*
 * ExerciseFolder.php - Vips exercise folder class for Stud.IP
 * Copyright (c) 2024  Elmar Ludwig
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

class ExerciseFolder extends StandardFolder
{
    /**
     * @param string|Object $range_id_or_object
     * @param string        $user_id
     * @return bool
     */
    public static function availableInRange($range_id_or_object, $user_id)
    {
        return false;
    }

    /**
     * @param string $user_id
     * @return bool
     */
    public function isReadable($user_id)
    {
        $exercise = Exercise::find($this->range_id);

        foreach ($exercise->tests as $test) {
            foreach ($test->assignments as $assignment) {
                if ($assignment->checkEditPermission($user_id) ||
                    $assignment->checkViewPermission($user_id) &&
                    ($assignment->checkAccess($user_id) || $assignment->releaseStatus($user_id) >= 3)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $user_id
     * @return bool
     */
    public function isWritable($user_id)
    {
        $exercise = Exercise::find($this->range_id);

        foreach ($exercise->tests as $test) {
            foreach ($test->assignments as $assignment) {
                if ($assignment->checkEditPermission($user_id)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $user_id
     * @return bool
     */
    public function isEditable($user_id)
    {
        return false;
    }

    /**
     * @param string $user_id
     * @return bool
     */
    public function isSubfolderAllowed($user_id)
    {
        return false;
    }

    /**
     * @param FileRef|string $fileref_or_id
     * @param string $user_id
     * @return bool
     */
    public function isFileDownloadable($fileref_or_id, $user_id)
    {
        return $this->isReadable($user_id);
    }

    /**
     * @param FileRef|string $fileref_or_id
     * @param string $user_id
     * @return bool
     */
    public function isFileEditable($fileref_or_id, $user_id)
    {
        return $this->isWritable($user_id);
    }

    /**
     * @param FileRef|string $fileref_or_id
     * @param string $user_id
     * @return bool
     */
    public function isFileWritable($fileref_or_id, $user_id)
    {
        return $this->isWritable($user_id);
    }
}
