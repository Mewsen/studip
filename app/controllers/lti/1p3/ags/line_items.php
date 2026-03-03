<?php

use Trails\Dispatcher;
use Studip\OAuth2\NegotiatesWithPsr7;
use OAT\Library\Lti1p3Core\Service\Server\LtiServiceServer;
use OAT\Library\Lti1p3Ags\Repository\LineItemRepositoryInterface;
use OAT\Library\Lti1p3Core\Security\OAuth2\Validator\RequestAccessTokenValidatorInterface;
use OAT\Library\Lti1p3Ags\Service\LineItem\Server\Handler\ListLineItemsServiceServerRequestHandler;
use OAT\Library\Lti1p3Ags\Service\LineItem\Server\Handler\CreateLineItemServiceServerRequestHandler;

class Lti_1p3_Ags_LineItemsController extends AuthenticatedController
{
    protected $allow_nobody = true;
    protected $with_session = false;
    use NegotiatesWithPsr7;

    public function __construct(
        protected Dispatcher $dispatcher,
        protected LineItemRepositoryInterface $lineItemRepo,
        protected RequestAccessTokenValidatorInterface $tokenValidator
    )
    {
        parent::__construct($dispatcher);
    }

    public function index_action(): void
    {
        $requestHandler = match (Request::method()) {
            'POST' => new CreateLineItemServiceServerRequestHandler($this->lineItemRepo),
            'GET' => new ListLineItemsServiceServerRequestHandler($this->lineItemRepo),
            default => throw new MethodNotAllowedException()
        };

        $serviceServer = new LtiServiceServer($this->tokenValidator, $requestHandler);

        $this->renderPsrResponse(
            $serviceServer->handle($this->getPsrRequest())
        );
    }

}
