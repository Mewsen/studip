<?php

namespace JsonApi\Routes\DashboardWidget;

use DashboardWidget\Container;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * DashboardWidget's Container Widgets index route handler.
 *
 * @author Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 */
class ContainerWidgetsIndex extends JsonApiController
{
    /**
     * @inheritdoc
     */
    // protected $allowedPagingParameters = ['offset', 'limit'];

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);
        if (!$container = Container::find($args['id'])) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canIndexContainerWidgets($user, $container)) {
            throw new AuthorizationFailedException();
        }

        $widgets = $container->widgets;
        return $this->getContentResponse($widgets);
    }
}
