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
 * DashboardWidget's Widgets update route handler.
 *
 * @author Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 */
class WidgetsUpdate extends JsonApiController
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

        if (!$resource = Widget::find($args['id'])) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canUpdateWidgets($user, $resource)) {
            throw new AuthorizationFailedException();
        }

        $payload = self::arrayGet($json, 'data.attributes.payload');
        $resource->setData(['payload' => $payload]);

        if (!$resource->widget_type->validatePayload((object) $payload)) {
            throw new UnsupportedRequestError('Invalid Widget\'s Payload');
        }

        $resource->store();

        return $this->getContentResponse($resource);
    }

    /**
     * @inheritdoc
     */
    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data.attributes.payload')) {
            return 'Attribute \'payload\' is required.';
        }
    }
}
