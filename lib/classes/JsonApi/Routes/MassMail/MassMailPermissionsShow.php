<?php

namespace JsonApi\Routes\MassMail;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;

/**
 * Displays settings for the given massmail permissions..
 */
class MassMailPermissionsShow extends JsonApiController
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        if (!$resource = \MassMail\MassMailPermission::find($args['id'])) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canShowMassMailPermissions($this->getUser($request), $resource)) {
            throw new AuthorizationFailedException();
        }


        return $this->getContentResponse($resource);
    }
}
