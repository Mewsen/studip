<?php
namespace Trails\Exceptions;

class DoubleRenderError extends \Trails\Exception
{
    public function __construct()
    {
        $message  = 'Render and/or redirect were called multiple times in this ';
        $message .= 'action. Please note that you may only call render OR ';
        $message .= 'redirect, and at most once per action.';
        parent::__construct(500, $message);
    }
}
