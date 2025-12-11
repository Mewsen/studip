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
 * @author Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 */
class ContainerCreate extends JsonApiController
{
    use ValidationTrait;

    /**
     * @inheritdoc
     */
    protected $allowedIncludePaths = ['owner', 'widgets'];

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->validate($request);
        $user = $this->getUser($request);
        $context = self::arrayGet($json, 'data.attributes.context');
        $contextId = Container::DEFAULT_CONTEXT_ID;
        if (self::arrayHas($json, 'data.attributes.context-id')) {
            $contextId = self::arrayGet($json, 'data.attributes.context-id');
        }

        if (!Authority::canCreateContainer($user)) {
            throw new AuthorizationFailedException();
        }

        if (!$resource = Container::ensureUserContextContainerExists($user->id, $context, $contextId)) {
            throw new RecordNotFoundException();
        }

        return $this->getContentResponse($resource);
    }

    /**
     * @inheritdoc
     */
    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data.attributes.context')) {
            return 'Attribute \'context\' is required.';
        }

        $context = self::arrayGet($json, 'data.attributes.context');
        if (!in_array($context, Container::ALL_CONTEXTS)) {
            return 'Invalid Dashboard Widget Context!';
        }
    }
}
