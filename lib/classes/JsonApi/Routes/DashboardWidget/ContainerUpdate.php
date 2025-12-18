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

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
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
        $filteredLayout = $this->filterLayout($layout);
        $breakpoint = self::arrayGet($json, 'data.attributes.breakpoint');

        $container->payload[$breakpoint] = $filteredLayout;
        $container->store();

        return $this->getContentResponse($container);
    }

    /**
     * @inheritdoc
     */
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
        $layout = self::arrayGet($json, 'data.attributes.layout');
        if (!is_array($layout)) {
            return 'Attribute \'layout\' must be an array containing dashboard-widgets position data.';
        }
        foreach ($layout as $index => $position) {
            if (!is_array($position) ||
                count(array_diff(['x', 'y', 'h', 'w', 'i'], array_keys($position))) > 0) {
                return "Invalid `layout.position` parameters at index '{$index}`.";
            }
        }
    }

    /**
     * Ensures the layout data contains needed values only.
     *
     * @param array $layout the raw layout array coming from request.
     * @return array
     */
    private function filterLayout(array $layout): array
    {
        $filteredLayout = [];
        foreach ($layout as $widgetPosition) {
            $widgetId = (int) $widgetPosition['i'];
            $filteredPosition = [
                'x' => $widgetPosition['x'],
                'y' => $widgetPosition['y'],
                'h' => $widgetPosition['h'],
                'w' => $widgetPosition['w'],
                'i' => $widgetId,
            ];
            // We make the array associative with widgetId, in order to prevent duplication!
            $filteredLayout[$widgetId] = $filteredPosition;
        }
        // We have to get rid of keys here!
        return array_values($filteredLayout);
    }
}
