<?php
namespace Trails\Exceptions;

class RoutingError extends \Trails\Exception
{
    public function __construct(string $message)
    {
        parent::__construct(400, $message);
    }
}
