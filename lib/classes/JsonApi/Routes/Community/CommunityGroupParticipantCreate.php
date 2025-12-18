<?php

namespace JsonApi\Routes\Community;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use \Community\CommunityGroup as CommunityGroup;
use \Community\CommunityGroupParticipant as CommunityGroupParticipant;


class CommunityGroupParticipantCreate extends JsonApiController
{
    use ValidationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->validate($request);
        $user = $this->getUser($request);
        $group = CommunityGroup::find(self::arrayGet($json, 'data.attributes.group-id'));
        if (!$group) {
            throw new RecordNotFoundException();
        }
        if (!Authority::canCreateCommunityGroupParticipant($user, $group)) {
            throw new AuthorizationFailedException();
        }

        $resource = $this->createGroupParticipant($json, $user, $group);

        return $this->getCreatedResponse($resource);
    }

    /**
     * Validates the resource document for a new participant.
     *
     * @param array $json The decoded JSON body.
     * @param mixed $data Additional data for validation.
     * @return string|null Error message or null if valid.
     */
    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data')) {
            return 'Missing `data` member at document´s top level.';
        }
        if (self::arrayHas($json, 'data.id')) {
            return 'New document must not have an `id`.';
        }

        if (!self::arrayHas($json, 'data.attributes.group-id')) {
            return 'Attribute \'group-id\' is required.';
        }
        if (!self::arrayHas($json, 'data.attributes.user-id')) {
            return 'Attribute \'user-id\' is required.';
        }
    }

    /**
     * Persists the participant entry with appropriate status.
     * If the group is public or the acting user is a moderator, status is set to MEMBER.
     * Otherwise, it defaults to PENDING.
     *
     * @param array $json The validated JSON data.
     * @param \User $user The acting user.
     * @param CommunityGroup $group The group to join.
     * @return CommunityGroupParticipant|null The created participant object.
     */
    private function createGroupParticipant($json, $user, $group): ?CommunityGroupParticipant
    {
        $group_id = self::arrayGet($json, 'data.attributes.group-id');
        $user_id = self::arrayGet($json, 'data.attributes.user-id');
        $role = CommunityGroupParticipant::ROLE_FOLLOWER;
        $status = CommunityGroupParticipant::STATUS_PENDING;

        if (!$group->is_private || $group->isModerator($user->id)) {
            $status = CommunityGroupParticipant::STATUS_MEMBER;
        }

        $participant = CommunityGroupParticipant::ensureRecordExists([
            'group_id' => $group_id,
            'user_id' => $user_id,
            'status' => $status,
            'role' => $role
        ]);

        return $participant;
    }
}
