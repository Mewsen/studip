<?php

namespace JsonApi\Routes\Community;

use Community\CommunityGroup;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CommunityGroupUpdate extends JsonApiController
{
    use ValidationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        $resource = CommunityGroup::find($args['id']);
        if (!$resource) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);
        if (!Authority::canUpdateCommunityGroup($user, $resource)) {
            throw new AuthorizationFailedException();
        }

        $json = $this->validate($request);
        $resource = $this->updateGroup($json, $resource);

        return $this->getContentResponse($resource);
    }

    /**
     * Validates the resource document for updating a community group.
     *
     * @param array $json The decoded JSON body.
     * @param mixed $data Additional data for validation.
     * @return string|null Error message or null if valid.
     */
    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data')) {
            return 'Missing `data` member at document\'s top level.';
        }

        if (!self::arrayHas($json, 'data.id')) {
            return 'Document must have an `id`.';
        }
    }

    /**
     * Updates the group attributes based on the provided JSON data.
     *
     * @param array $json The validated JSON data.
     * @param CommunityGroup $group The group to update.
     * @return CommunityGroup The updated group object.
     */
    private function updateGroup($json, CommunityGroup $group): CommunityGroup
    {
        $attributes = self::arrayGet($json, 'data.attributes');

        // Update only if the attribute is present in the request
        if (isset($attributes['name'])) {
            $group->name = $attributes['name'];
        }

        if (isset($attributes['description'])) {
            $group->description = $attributes['description'];
        }

        if (isset($attributes['is-private'])) {
            $group->is_private = $attributes['is-private'];
        }

        if (isset($attributes['status'])) {
            $group->status = $attributes['status'];
        }

        if ($group->isDirty()) {
            $group->store();
        }

        return $group;
    }
}
