<?php

use Trails\Dispatcher;
use Studip\OAuth2\NegotiatesWithPsr7;
use Studip\Lti\LTI1p3\LineItemRepository;
use Studip\Lti\LTI1p3\RegistrationManager;
use OAT\Library\Lti1p3Core\Service\Server\LtiServiceServer;
use OAT\Library\Lti1p3Ags\Repository\LineItemRepositoryInterface;
use OAT\Library\Lti1p3Core\Security\OAuth2\Validator\RequestAccessTokenValidator;
use OAT\Library\Lti1p3Core\Service\Server\Handler\LtiServiceServerRequestHandlerInterface;
use OAT\Library\Lti1p3Ags\Service\LineItem\Server\Handler\GetLineItemServiceServerRequestHandler;
use OAT\Library\Lti1p3Ags\Service\LineItem\Server\Handler\DeleteLineItemServiceServerRequestHandler;
use OAT\Library\Lti1p3Ags\Service\LineItem\Server\Handler\UpdateLineItemServiceServerRequestHandler;

class Lti_1p3_Ags_LineItemController extends AuthenticatedController
{
    protected $allow_nobody = true;
    protected $with_session = false;
    protected LineItemRepositoryInterface $lineItemRepo;

    use NegotiatesWithPsr7;

    public function __construct(Dispatcher $dispatcher)
    {
        parent::__construct($dispatcher);

        $this->lineItemRepo = new LineItemRepository();
    }

    public function index_action(): void
    {
        $requestHandler = match (Request::method()) {
            'PUT' => new UpdateLineItemServiceServerRequestHandler($this->lineItemRepo),
            'DELETE' => new DeleteLineItemServiceServerRequestHandler($this->lineItemRepo),
            'GET' => new GetLineItemServiceServerRequestHandler($this->lineItemRepo),
            default => throw new MethodNotAllowedException()
        };

        $this->renderAgsResponse($requestHandler);
    }

    public function results_action(): void
    {
    }

    private function renderAgsResponse(
        LtiServiceServerRequestHandlerInterface $requestHandler
    ): void
    {
        $requestValidator = new RequestAccessTokenValidator(new RegistrationManager());

        $server = new LtiServiceServer($requestValidator, $requestHandler);
        $this->renderPsrResponse($server->handle($this->getPsrRequest()));
    }
}
