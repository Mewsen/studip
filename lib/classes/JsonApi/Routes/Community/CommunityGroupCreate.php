<?php

namespace JsonApi\Routes\Community;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use \Community\CommunityGroup as CommunityGroup;
use \Community\CommunityGroupParticipant as CommunityGroupParticipant;

class CommunityGroupCreate extends JsonApiController
{
    use ValidationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->validate($request);
        $user = $this->getUser($request);

        if (!Authority::canCreateCommunityGroup($user)) {
            throw new AuthorizationFailedException();
        }

        $resource = self::createGroup($json, $user);

        return $this->getContentResponse($resource);
    }

    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data')) {
            return 'Missing `data` member at document´s top level.';
        }
        if (self::arrayHas($json, 'data.id')) {
            return 'New document must not have an `id`.';
        }

        if (!self::arrayHas($json, 'data.attributes.name')) {
            return 'Attribute \'name\' is required.';
        }
        if (!self::arrayHas($json, 'data.attributes.description')) {
            return 'Attribute \'description\' is required.';
        }
    }

    /**
     * Persists the group and adds the creator as the initial moderator.
     *
     * @param array $json The validated JSON data.
     * @param \User $user The creating user.
     * @return CommunityGroup The created group object.
     */
    private function createGroup($json, $user): ?CommunityGroup
    {
        $name = self::arrayGet($json, 'data.attributes.name');
        $description = self::arrayGet($json, 'data.attributes.description');
        $creator_id = $user->id;
        $is_private = self::arrayGet($json, 'data.attributes.is-private', '0');
        $status = CommunityGroup::STATUS_ACTIVE;

        $group = CommunityGroup::create([
            'name' => $name,
            'description' => $description,
            'creator_id' => $creator_id,
            'is_private' => $is_private,
            'status' => $status
        ]);

        CommunityGroupParticipant::create([
            'group_id' => $group->id,
            'user_id' => $user->id,
            'status' => CommunityGroupParticipant::STATUS_MEMBER,
            'role' => CommunityGroupParticipant::ROLE_MODERATOR
        ]);

        return $group;
    }
}
