<?php

namespace JsonApi\Routes\Contacts;

use JsonApi\JsonApiController;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ContactGroupsDelete extends JsonApiController
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $resource = \ContactGroup::find($args['id']);

        if (!$resource) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);

        if (!Authority::canDeleteGroups($user, $resource)) {
            throw new AuthorizationFailedException();
        }

        $resource->delete();

        return $this->getCodeResponse(204);
    }
}