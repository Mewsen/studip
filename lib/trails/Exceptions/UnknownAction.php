<?php
namespace Trails\Exceptions;

class UnknownAction extends \Trails\Exception
{
    public function __construct(string $message)
    {
        parent::__construct(404, $message);
    }
}
