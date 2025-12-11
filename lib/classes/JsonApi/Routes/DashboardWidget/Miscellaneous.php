<?php

namespace JsonApi\Routes\DashboardWidget;

use DashboardWidget\Container;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\NonJsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * DashboardWidget's Miscellaneous route handler.
 *
 * @author Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 */
class Miscellaneous extends NonJsonApiController
{
    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $user = $this->getUser($request);
        if (!Authority::canFetchMisc($user)) {
            throw new AuthorizationFailedException();
        }

        $response->getBody()->write(json_encode(Container::getMiscellaneous()));

        return $response->withHeader('Content-type', 'application/json');
    }
}
