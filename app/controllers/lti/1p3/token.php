<?php

use Studip\Lti\LTI1p3\PlatformManager;
use Studip\Lti\Controller\PlatformBaseController;
use OAT\Library\Lti1p3Core\Security\OAuth2\Generator\AccessTokenResponseGeneratorInterface;

final class Lti_1p3_TokenController extends PlatformBaseController
{
    public function index_action(): void
    {
        $tokenGenerator = app()->get(AccessTokenResponseGeneratorInterface::class);

        $response = $tokenGenerator->generate(
            $this->getPsrRequest(),
            $this->getPsrResponse(),
            PlatformManager::getKeyChain()->getIdentifier()
        );

        $this->renderPsrResponse($response);
    }
}
