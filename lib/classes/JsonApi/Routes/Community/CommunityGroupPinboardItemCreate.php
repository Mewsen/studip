<?php

namespace JsonApi\Routes\Community;

use Community\CommunityGroup;
use Community\CommunityGroupPinboardItem;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CommunityGroupPinboardItemCreate extends JsonApiController
{
    use ValidationTrait;

    /**
     * Create a new pinboard item.
     * The group association is provided via 'group_id' in the request attributes.
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->validate($request);
        $user = $this->getUser($request);

        $group_id = self::arrayGet($json, 'data.attributes.group_id');
        $group = CommunityGroup::find($group_id);

        if (!$group) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canCreatePinboardItem($user, $group)) {
            throw new AuthorizationFailedException();
        }

        $resource = $this->createPinboardItem($json, $user, $group);

        return $this->getContentResponse($resource);
    }

    /**
     * Validates the resource document.
     */
    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data')) {
            return 'Missing `data` member at document\'s top level.';
        }

        if (!self::arrayHas($json, 'data.attributes.group_id')) {
            return 'Attribute \'group_id\' is required.';
        }

        if (!self::arrayHas($json, 'data.attributes.payload')) {
            return 'Attribute \'payload\' is required.';
        }

        if (!self::arrayHas($json, 'data.attributes.item-type')) {
            return 'Attribute \'item-type\' is required.';
        }
    }

    /**
     * Persists the pinboard item.
     */
    private function createPinboardItem($json, $user, $group): CommunityGroupPinboardItem
    {
        return CommunityGroupPinboardItem::create([
            'group_id' => $group->id,
            'user_id'  => $user->id,
            'payload'  => self::arrayGet($json, 'data.attributes.payload'),
            'item_type' => self::arrayGet($json, 'data.attributes.item-type'),
            'file_ref_id' => self::arrayGet($json, 'data.attributes.file-ref-id', ''),
        ]);
    }
}