<?php

namespace JsonApi\Routes\DashboardWidget;

use DashboardWidget\Container;
use DashboardWidget\Widget;
use DashboardWidget\WidgetTypes\WidgetType;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\UnsupportedRequestError;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * DashboardWidget's Container Widgets create route handler.
 *
 * @author Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 */
class ContainerWidgetsCreate extends JsonApiController
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

        if (!Authority::canCreateContainerWidgets($user, $container)) {
            throw new AuthorizationFailedException();
        }

        $widgetType = self::arrayGet($json, 'data.attributes.widget-type');
        $widgetScope = self::arrayGet($json, 'data.attributes.widget-scope');
        $payload = self::arrayGet($json, 'data.attributes.payload');
        $breakpoint = self::arrayGet($json, 'data.attributes.breakpoint');
        $position = self::arrayGet($json, 'data.attributes.position');

        $resource = Widget::build([
            'container_id' => $container->id,
            'type' => $widgetType,
            'scope' => $widgetScope,
            'payload' => $payload,
        ]);

        if (!$resource->widget_type->validatePayload((object) $payload)) {
            throw new UnsupportedRequestError('Invalid Widget\'s Payload');
        }

        $resource->store();
        $container->addUpdateWidgetInPayload($resource, $breakpoint, $position, true);

        $container->store();

        return $this->getCreatedResponse($resource);
    }

    /**
     * @inheritdoc
     */
    protected function validateResourceDocument($json, $data)
    {
        if (!$widgetType = self::arrayGet($json, 'data.attributes.widget-type')) {
            return 'Attribute \'widget-type\' is required.';
        }

        if (!$widgetScope = self::arrayGet($json, 'data.attributes.widget-scope')) {
            return 'Attribute \'widget-scope\' is required.';
        }

        $isTypeValid = WidgetType::isOfTypeWithScope($widgetType, $widgetScope);
        if ($isTypeValid === null) {
            return 'Undefined widget `type` or `scope`.';
        }

        if (!self::arrayHas($json, 'data.attributes.payload')) {
            return 'Attribute \'payload\' is required.';
        }

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
