<?php

namespace JsonApi\Routes\Community;

use Community\CommunityGroupPinboardItem;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CommunityGroupPinboardItemUpdate extends JsonApiController
{
    use ValidationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        $resource = CommunityGroupPinboardItem::find($args['id']);
        if (!$resource) {
            throw new RecordNotFoundException();
        }

        $json = $this->validate($request);
        $user = $this->getUser($request);

        if (!Authority::canUpdatePinboardItem($user, $resource)) {
            throw new AuthorizationFailedException();
        }

        $resource = $this->updateItem($json, $resource);

        return $this->getContentResponse($resource);
    }

    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data')) {
            return 'Missing `data` member at document\'s top level.';
        }
    }

    private function updateItem($json, $resource): CommunityGroupPinboardItem
    {
        $attributes = $json['data']['attributes'];

        if (isset($attributes['payload'])) {
            $resource->payload = $attributes['payload'];
        }

        if (isset($attributes['file-ref-id'])) {
            $resource->file_ref_id = $attributes['file-ref-id'];
        }

        
        if ($resource->isDirty()) {
            $resource->store();
        }

        return $resource;
    }
}