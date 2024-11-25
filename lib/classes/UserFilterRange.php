<?php

/**
 * UserFilterRange.php
 *
 * An interface that provides information about necessary permissions for editing a UserFilter object.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Thomas Hackl <thomas.hackl@uni-passau.de>
 * @since       6.0
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */

interface UserFilterRange {

    /**
     * Check whether the given user can edit the given UserFilter object.
     * @param User $user
     * @param UserFilter $filter
     * @return bool
     */
    public function canEditFilter(User $user, UserFilter $filter): bool;

}
