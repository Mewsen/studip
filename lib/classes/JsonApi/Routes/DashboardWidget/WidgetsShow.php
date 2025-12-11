<?php

namespace JsonApi\Routes\DashboardWidget;

use DashboardWidget\Widget;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * DashboardWidget's Widget show route handler.
 *
 * @author Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 */
class WidgetsShow extends JsonApiController
{
    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);

        if (!$resource = Widget::find($args['id'])) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canShowWidgets($user, $resource)) {
            throw new AuthorizationFailedException();
        }

        return $this->getContentResponse($resource);
    }
}
