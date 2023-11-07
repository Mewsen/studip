<?php
/**
 * EnrolmentInformation.class.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Moritz Strohm <strohm@data-quest.de>
 * @copyright   2023
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 *
 */

namespace Studip;

use Studip\Information;

/**
 * The EnrolmentInformation class holds information regarding the ability
 * of a user to enrol into a specific course.
 */
class EnrolmentInformation extends Information
{
    /**
     * @var bool An indicator whether enrolment is allowed according
     * to the message (true) or forbidden (false).
     */
    protected bool $enrolment_allowed = false;

    public function __construct(
        string $message,
        int $type = Information::INFO,
        string $codeword = '',
        bool $enrolment_allowed = false
    ) {
        $this->enrolment_allowed = $enrolment_allowed;
        parent::__construct($message, $type, $codeword);
    }

    /**
     * The setter for the enrolment_allowed attribute.
     *
     * @param bool $enrolment_allowed The new status for the enrolment_allowed attribute.
     *
     * @return void
     */
    public function setEnrolmentAllowed(bool $enrolment_allowed) : void
    {
        $this->enrolment_allowed = $enrolment_allowed;
    }

    /**
     * The getter for the enrolment_allowed attribute.
     *
     * @return bool The status of the enrolment_allowed attribute.
     */
    public function isEnrolmentAllowed() : bool
    {
        return $this->enrolment_allowed;
    }
}
