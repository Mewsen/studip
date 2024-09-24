<?php

namespace exTpl;

use Exception;

/**
 * Exception class used to report template parse errors.
 */
class TemplateParserException extends Exception
{
    public function __construct(string $message, Scanner $scanner)
    {
        $type  = $scanner->tokenType();
        $value = is_int($type) ? $scanner->tokenValue() : $type;

        return parent::__construct("$message at \"$value\"");
    }
}
