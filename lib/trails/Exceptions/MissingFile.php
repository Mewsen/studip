<?php
namespace Trails\Exceptions;

class MissingFile extends \Trails\Exception
{
    public function __construct(string $message)
    {
        parent::__construct(500, $message);
    }
}
