<?php

use Studip\Lti\Controller\PlatformBaseController;
use OAT\Library\Lti1p3Core\Security\Oidc\Server\OidcAuthenticationRequestHandler;

final class Lti_1p3_LoginController extends PlatformBaseController
{
    public function index_action(): void
    {
        $oidcLoginHandler = app()->get(OidcAuthenticationRequestHandler::class);

        $this->renderPsrResponse(
            $oidcLoginHandler->handle($this->getPsrRequest())
        );
    }
}
