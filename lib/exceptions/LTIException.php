<?php

namespace Studip;

use Studip\Exception;

/**
 * The LTIException class represents exceptions that occur in the Stud.IP LTI interface.
 */
class LTIException extends Exception
{
    /**
     * The REGISTRATION_NOT_LINKED_TO_TOOL status code represents the case where
     * a LTI tool registration is not linked to a tool.
     */
    public const REGISTRATION_NOT_LINKED_TO_TOOL = 1;
}
