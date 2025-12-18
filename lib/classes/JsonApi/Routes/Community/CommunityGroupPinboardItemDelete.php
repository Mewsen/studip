<?php

namespace JsonApi\Routes\Community;

use Community\CommunityGroupPinboardItem;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CommunityGroupPinboardItemDelete extends JsonApiController
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $resource = CommunityGroupPinboardItem::find($args['id']);
        if (!$resource) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);

        if (!Authority::canDeletePinboardItem($user, $resource)) {
            throw new AuthorizationFailedException();
        }

        $resource->delete();

        return $this->getCodeResponse(204);
    }
}