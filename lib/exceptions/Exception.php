<?php

/**
 * Exception.class.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Moritz Strohm <strohm@data-quest.de>
 * @copyright   2019-2024
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */

namespace Studip;

/**
 * This class is a specialisation of the standard Exception class
 * to distinguish Stud.IP exceptions from standard exceptions.
 */
class Exception extends \Exception
{
    /**
     * GENERAL_ERROR means that an unspecified error has occurred.
     */
    const GENERAL_ERROR = 0;

    /**
     * END_BEFORE_BEGINNING means that a time range is specified
     * where the end lies before the beginning.
     */
    const END_BEFORE_BEGINNING = 1;

    protected ?\Range $range = null;

    public function __construct(string $message = '', int $code = 0, ?\Range $range = null)
    {
        parent::__construct($message, $code);
        $this->range = $range;
    }

    /**
     * Converts the content of the exception into an Information object.
     *
     * @return Information An Information representation of the exception.
     */
    public function getInformation() : Information
    {
        return new Information(
            $this->getMessage(),
            \Studip\Information::ERROR,
            (string) $this->getCode(),
            $this->range
        );
    }
}
