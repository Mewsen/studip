<?php

namespace JsonApi\Routes\Community;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use \Community\CommunityGroup as CommunityGroup;

class CommunityGroupShow extends JsonApiController
{
    protected $allowedIncludePaths = ['participants', 'pinboard-items'];

    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);
        $resource = CommunityGroup::find($args['id']);

        if (!$resource) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canShowCommunityGroup($user, $resource)) {
            throw new AuthorizationFailedException();
        }

        return $this->getContentResponse($resource);
    }
}