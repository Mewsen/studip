<?php
namespace Studip\Lti\Controller;

use AuthenticatedController;
use Studip\OAuth2\NegotiatesWithPsr7;

abstract class PlatformBaseController extends AuthenticatedController
{
    protected $allow_nobody = true;
    protected $with_session = false;

    use NegotiatesWithPsr7;
}
