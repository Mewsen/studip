<?php

use Studip\Lti\LTI1p3\PlatformManager;
use Studip\Lti\Controller\PlatformBaseController;
use OAT\Library\Lti1p3Core\Security\Jwks\Server\JwksRequestHandler;

final class Lti_1p3_JwksController extends PlatformBaseController
{
    public function index_action(): void
    {
        $platformKeyring = PlatformManager::getKeyChain();
        $jwksRequestHandler = app()->get(JwksRequestHandler::class);

        $this->renderPsrResponse(
            $jwksRequestHandler->handle($platformKeyring->getKeySetName())
        );
    }
}
