<?php

use Studip\Lti\Controller\AgsBaseController;
use OAT\Library\Lti1p3Ags\Service\Score\Server\Handler\ScoreServiceServerRequestHandler;
use OAT\Library\Lti1p3Ags\Service\Result\Server\Handler\ResultServiceServerRequestHandler;
use OAT\Library\Lti1p3Ags\Service\LineItem\Server\Handler\GetLineItemServiceServerRequestHandler;
use OAT\Library\Lti1p3Ags\Service\LineItem\Server\Handler\DeleteLineItemServiceServerRequestHandler;
use OAT\Library\Lti1p3Ags\Service\LineItem\Server\Handler\UpdateLineItemServiceServerRequestHandler;

final class Lti_1p3_Ags_LineItemController extends AgsBaseController
{
    public function index_action(): void
    {
        $requestHandler = match (Request::method()) {
            'PUT' => app()->get(UpdateLineItemServiceServerRequestHandler::class),
            'DELETE' => app()->get(DeleteLineItemServiceServerRequestHandler::class),
            'GET' => app()->get(GetLineItemServiceServerRequestHandler::class),
            default => throw new MethodNotAllowedException()
        };

        $this->renderAgsResponse($requestHandler);
    }

    public function scores_action(): void
    {
        $this->renderAgsResponse(
            app()->get(ScoreServiceServerRequestHandler::class)
        );
    }

    public function results_action(): void
    {
        $this->renderAgsResponse(
            app()->get(ResultServiceServerRequestHandler::class)
        );
    }
}
