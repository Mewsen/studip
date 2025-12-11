<?php

namespace JsonApi\Routes\DashboardWidget;

use DashboardWidget\Container;
use DashboardWidget\Widget;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\UnsupportedRequestError;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * DashboardWidget's Container Widgets update route handler.
 *
 * @author Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 */
class ContainerWidgetsUpdate extends JsonApiController
{
    use ValidationTrait;

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->validate($request);
        $user = $this->getUser($request);

        if (!$container = Container::find($args['id'])) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canUpdateContainerWidgets($user, $container)) {
            throw new AuthorizationFailedException();
        }

        if (!$resource = $container->widgets->find($args['widget_id'])) {
            throw new RecordNotFoundException();
        }

        // Optionally update payload over this endpoint.
        if (self::arrayHas($json, 'data.attributes.payload')) {
            $payload = self::arrayGet($json, 'data.attributes.payload');
            $resource->setData(['payload' => $payload]);

            if (!$resource->widget_type->validatePayload((object) $payload)) {
                throw new UnsupportedRequestError('Invalid Widget\'s Payload');
            }

            $resource->store();
        }

        $breakpoint = self::arrayGet($json, 'data.attributes.breakpoint');
        $position = self::arrayGet($json, 'data.attributes.position');

        $container->addUpdateWidgetInPayload($resource, $breakpoint, $position, false);

        $container->store();

        return $this->getContentResponse($resource);
    }

    /**
     * @inheritdoc
     */
    protected function validateResourceDocument($json, $data)
    {
        // TODO: atm payload should be optional here, but we can also make it required!?
        // if (!self::arrayHas($json, 'data.attributes.payload')) {
        //     return 'Attribute \'payload\' is required.';
        // }

        if (!self::arrayHas($json, 'data.attributes.breakpoint')) {
            return 'Attribute \'breakpoint\' is required.';
        }

        $breakpoint = self::arrayGet($json, 'data.attributes.breakpoint');
        if (!in_array($breakpoint, Container::ALL_BREAKPOINTS)) {
            return 'Undefined `breakpoint`.';
        }

        if (!self::arrayHas($json, 'data.attributes.position')) {
            return 'Attribute \'position\' is required.';
        }

        $position = self::arrayGet($json, 'data.attributes.position');
        if (!is_array($position) ||
            count(array_diff(['x', 'y', 'h', 'w'], array_keys($position))) > 0) {
            return 'Invalid `position` parameters.';
        }
    }
}
