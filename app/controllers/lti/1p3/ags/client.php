<?php
use Studip\OAuth2\NegotiatesWithPsr7;

class Lti_1p3_Ags_ClientController extends AuthenticatedController
{
    protected $allow_nobody = true;
    protected $with_session = false;

    use NegotiatesWithPsr7;
}
