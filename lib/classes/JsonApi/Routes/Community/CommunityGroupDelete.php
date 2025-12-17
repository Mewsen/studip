<?php

namespace JsonApi\Routes\Community;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use \Community\CommunityGroup as CommunityGroup;

class CommunityGroupDelete extends JsonApiController
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);
        $resource = CommunityGroup::find($args['id']);
        if (!$resource) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canDeleteCommunityGroup($user, $resource)) {
            throw new AuthorizationFailedException();
        }

        $resource->delete();

        return $this->getCodeResponse(204);
    }
}