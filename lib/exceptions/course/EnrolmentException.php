<?php
/**
 * EnrolmentException.class.php
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

class EnrolmentException extends Exception
{
    /**
     * ALREADY_MEMBER means that enrolment failed because the user
     * is already a member of the course.
     */
    const ALREADY_MEMBER = 1;

    /**
     * INVALID_PERMISSION_LEVEL means that the permission level of the user
     * in the course is invalid.
     */
    const INVALID_PERMISSION_LEVEL = 2;

    /**
     * PROMOTION_NOT_POSSIBLE means that the user cannot get a higher permission
     * level in the course.
     */
    const PROMOTION_NOT_POSSIBLE = 3;

    /**
     * DEMOTION_NOT_POSSIBLE means that the user cannot get a lower permission
     * level in the course.
     */
    const DEMOTION_NOT_POSSIBLE = 4;

    /**
     * NO_INSTITUTE_MEMBER means that enrolment failed because the user
     * is not the member of an institute the course is assigned to.
     */
    const NO_INSTITUTE_MEMBER = 5;

    /**
     * COURSE_IS_FULL means that no free seat is available for enrolling
     * another user.
     */
    const COURSE_IS_FULL = 10;

    /**
     * ADD_AWAITING_FAILED means that adding a user to the wait list failed.
     */
    const ADD_AWAITING_FAILED = 11;
}
