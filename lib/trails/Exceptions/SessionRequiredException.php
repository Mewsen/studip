<?php
namespace Trails\Exceptions;

class SessionRequiredException extends \Trails\Exception
{
    public function __construct()
    {
        $message = 'Tried to access a non existing session.';
        parent::__construct(500, $message);
    }
}
