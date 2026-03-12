<?php

use Studip\Lti\Controller\AgsBaseController;
use OAT\Library\Lti1p3Ags\Service\LineItem\Server\Handler\ListLineItemsServiceServerRequestHandler;
use OAT\Library\Lti1p3Ags\Service\LineItem\Server\Handler\CreateLineItemServiceServerRequestHandler;

final class Lti_1p3_Ags_LineItemsController extends AgsBaseController
{
    public function index_action(): void
    {
        $requestHandler = match (Request::method()) {
            'POST' => app()->get(CreateLineItemServiceServerRequestHandler::class),
            'GET' => app()->get(ListLineItemsServiceServerRequestHandler::class),
            default => throw new MethodNotAllowedException()
        };

        $this->renderAgsResponse($requestHandler);
    }
}
