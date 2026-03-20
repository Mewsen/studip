<?php

use Studip\Lti\Controller\EnrollBaseController;
use OAT\Library\Lti1p3Core\Security\Oidc\Server\OidcInitiationRequestHandler;

final class Enroll_Lti_AuthInitController extends EnrollBaseController
{
    protected $with_session = false;

    public function index_action(): void
    {
        $oidcInitHandler = app()->get(OidcInitiationRequestHandler::class);

        $this->renderPsrResponse(
            $oidcInitHandler->handle($this->getPsrRequest())
        );
    }
}
