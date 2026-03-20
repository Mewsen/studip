<?php
namespace Studip\Lti\Controller;

use AuthenticatedController;
use Studip\OAuth2\NegotiatesWithPsr7;
use OAT\Library\Lti1p3Core\Service\Server\LtiServiceServer;
use OAT\Library\Lti1p3Core\Service\Server\Handler\LtiServiceServerRequestHandlerInterface;
use OAT\Library\Lti1p3Core\Security\OAuth2\Validator\RequestAccessTokenValidatorInterface;

abstract class AgsBaseController extends AuthenticatedController
{
    protected $allow_nobody = true;
    protected $with_session = false;

    use NegotiatesWithPsr7;

    protected function renderAgsResponse(
        LtiServiceServerRequestHandlerInterface $requestHandler
    ): void
    {
        $serviceServer = new LtiServiceServer(
            app()->get(RequestAccessTokenValidatorInterface::class),
            $requestHandler
        );

        $this->renderPsrResponse(
            $serviceServer->handle($this->getPsrRequest())
        );
    }
}
