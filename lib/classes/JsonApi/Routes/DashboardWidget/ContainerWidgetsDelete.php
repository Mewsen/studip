<?php

namespace JsonApi\Routes\DashboardWidget;

use DashboardWidget\Container;
use DashboardWidget\Widget;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * DashboardWidget's Container Widgets delete route handler.
 *
 * @author Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 */
class ContainerWidgetsDelete extends JsonApiController
{
    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);
        $container = $container = Container::find($args['id']);

        if (!$container) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canDeleteContainerWidgets($user, $container)) {
            throw new AuthorizationFailedException();
        }

        $resource = $container->widgets->find($args['widget_id']);

        if (!$resource) {
            throw new RecordNotFoundException();
        }

        $resource->delete();

        return $this->getCodeResponse(204);
    }
}
