<?php

use Trails\Dispatcher;
use Studip\OAuth2\NegotiatesWithPsr7;
use OAT\Library\Lti1p3Core\Security\Oidc\Server\OidcAuthenticationRequestHandler;

class Lti_1p3_LoginController extends AuthenticatedController
{
    protected $allow_nobody = true;
    protected $with_session = false;
    use NegotiatesWithPsr7;

    public function __construct(
        protected Dispatcher $dispatcher,
        protected OidcAuthenticationRequestHandler $oidcLoginHandler
    )
    {
        parent::__construct($dispatcher);
    }

    public function index_action(): void
    {
        $this->renderPsrResponse(
            $this->oidcLoginHandler->handle($this->getPsrRequest())
        );
    }
}
