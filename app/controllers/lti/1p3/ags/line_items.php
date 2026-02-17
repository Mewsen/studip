<?php

use Studip\OAuth2\NegotiatesWithPsr7;
use Studip\Lti\LTI1p3\LineItemRepository;
use Studip\Lti\LTI1p3\RegistrationManager;
use OAT\Library\Lti1p3Core\Service\Server\LtiServiceServer;
use OAT\Library\Lti1p3Ags\Repository\LineItemRepositoryInterface;
use OAT\Library\Lti1p3Core\Security\OAuth2\Validator\RequestAccessTokenValidator;
use OAT\Library\Lti1p3Ags\Service\LineItem\Server\Handler\ListLineItemsServiceServerRequestHandler;
use OAT\Library\Lti1p3Ags\Service\LineItem\Server\Handler\CreateLineItemServiceServerRequestHandler;

class Lti_1p3_Ags_LineItemsController extends AuthenticatedController
{
    protected $allow_nobody = true;
    protected $with_session = false;
    use NegotiatesWithPsr7;

    public function index_action(): void
    {
        $requestHandler = match (Request::method()) {
            'POST' => new CreateLineItemServiceServerRequestHandler(new LineItemRepository()),
            'GET' => new ListLineItemsServiceServerRequestHandler(new LineItemRepository()),
            default => throw new MethodNotAllowedException()
        };

        $requestValidator = new RequestAccessTokenValidator(new RegistrationManager());

        $server = new LtiServiceServer($requestValidator, $requestHandler);

        $this->renderPsrResponse($server->handle($this->getPsrRequest()));
    }

}
