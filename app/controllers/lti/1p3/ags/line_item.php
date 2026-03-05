<?php

use Trails\Dispatcher;
use Studip\OAuth2\NegotiatesWithPsr7;
use OAT\Library\Lti1p3Core\Service\Server\LtiServiceServer;
use OAT\Library\Lti1p3Ags\Repository\LineItemRepositoryInterface;
use OAT\Library\Lti1p3Core\Service\Server\Handler\LtiServiceServerRequestHandlerInterface;
use OAT\Library\Lti1p3Core\Security\OAuth2\Validator\RequestAccessTokenValidatorInterface;
use OAT\Library\Lti1p3Ags\Service\LineItem\Server\Handler\GetLineItemServiceServerRequestHandler;
use OAT\Library\Lti1p3Ags\Service\LineItem\Server\Handler\DeleteLineItemServiceServerRequestHandler;
use OAT\Library\Lti1p3Ags\Service\LineItem\Server\Handler\UpdateLineItemServiceServerRequestHandler;

final class Lti_1p3_Ags_LineItemController extends AuthenticatedController
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
        try {
            $requestHandler = match (Request::method()) {
                'PUT' => new UpdateLineItemServiceServerRequestHandler($this->lineItemRepo),
                'DELETE' => new DeleteLineItemServiceServerRequestHandler($this->lineItemRepo),
                'GET' => new GetLineItemServiceServerRequestHandler($this->lineItemRepo),
                default => throw new MethodNotAllowedException()
            };

            $this->renderAgsResponse($requestHandler);

        } catch (\Throwable $e) {
            $requestBody = $this->getPsrRequest()->getBody()->getContents();

            $response = new \Nyholm\Psr7\Response(
            500,
            ['Content-Type' => 'application/json'],
            json_encode([
            'message' => $e->getMessage(),
            'request_body' => $requestBody,
            ])
            );

            $this->renderPsrResponse($response);
        }

        $this->set_layout(null);
    }

    public function results_action(): void
    {
    }

    private function renderAgsResponse(
        LtiServiceServerRequestHandlerInterface $requestHandler
    ): void
    {
        $serviceServer = new LtiServiceServer($this->tokenValidator, $requestHandler);

        $this->renderPsrResponse(
            $serviceServer->handle($this->getPsrRequest())
        );
    }
}
