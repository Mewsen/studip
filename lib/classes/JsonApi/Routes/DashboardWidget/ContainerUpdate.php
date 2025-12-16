<?php

namespace JsonApi\Routes\DashboardWidget;

use DashboardWidget\Container;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\ValidationTrait;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * DashboardWidget's Container create route handler.
 *
 * @author Ron Lucke <lucke@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 */

class ContainerUpdate extends JsonApiController
{
    use ValidationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        $container = Container::find($args['id']);
        if (!$container) {
            throw new RecordNotFoundException();
        }
        $json = $this->validate($request);
        $user = $this->getUser($request);
        if (!Authority::canManageContainerWidgets($user, $container)) {
            throw new AuthorizationFailedException();
        }

        $layout = self::arrayGet($json, 'data.attributes.layout');
        $breakpoint = self::arrayGet($json, 'data.attributes.breakpoint');

        $container->payload[$breakpoint] = $layout;
        $container->store();

        return $this->getContentResponse($container);
    }

    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data.attributes.breakpoint')) {
            return 'Attribute \'breakpoint\' is required.';
        }
        $breakpoint = self::arrayGet($json, 'data.attributes.breakpoint');
        if (!in_array($breakpoint, Container::ALL_BREAKPOINTS)) {
            return 'Undefined `breakpoint`.';
        }
        if (!self::arrayHas($json, 'data.attributes.layout')) {
            return 'Attribute \'layout\' is required.';
        }
    }
}
