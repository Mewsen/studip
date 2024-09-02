<?php
/**
 * MembershipException.class.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Moritz Strohm <strohm@data-quest.de>
 * @copyright   2023-2024
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */

namespace Studip;

class MembershipException extends Exception
{
    /**
     * NOT_A_MEMBER means that the user is not a member of the course.
     */
    const NOT_A_MEMBER = 1;

    /**
     * REMOVAL_FAILED means that the removal of the user from the course
     * was unsuccessful.
     */
    const REMOVAL_FAILED = 2;

    /**
     * USER_IS_SOLE_LECTURER means that the user that shall be removed
     * from the course is the sole lecturer of the course.
     */
    const USER_IS_SOLE_LECTURER = 2;

    /**
     * MOVING_POSITION_FAILED means that moving a course member to
     * another position was unsuccessful.
     */
    const MOVING_POSITION_FAILED = 10;
}
