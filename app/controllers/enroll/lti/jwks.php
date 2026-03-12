<?php

use Studip\Lti\LTI1p3\ToolManager;
use Studip\Lti\Controller\EnrollBaseController;
use OAT\Library\Lti1p3Core\Security\Jwks\Server\JwksRequestHandler;

final class Enroll_Lti_JwksController extends EnrollBaseController
{
    protected $with_session = false;

    public function index_action(): void
    {
        $toolKeyring = ToolManager::getKeyChain();

        $jwksRequestHandler = app()->get(JwksRequestHandler::class);

        $this->renderPsrResponse(
            $jwksRequestHandler->handle($toolKeyring->getKeySetName())
        );
    }
}
