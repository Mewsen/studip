<?php

namespace Studip;

use Studip\Exception;

/**
 * The KeyringException class represents exceptions that occurr when using keyrings.
 */
class KeyringException extends Exception
{
    /**
     * The CREATION_FAILED status code means that a keyring could not be created.
     */
    public const CREATION_FAILED = 1;

    /**
     * The NOT FOUND status code means that the search for a keyring did not yield a result.
     */
    public const NOT_FOUND = 2;

    /**
     * The UNSUPPORTED_KEY_ALGORITHM status code means that the selected key algorithm
     * is not supported.
     */
    public const UNSUPPORTED_KEY_ALGORITHM = 3;
}
