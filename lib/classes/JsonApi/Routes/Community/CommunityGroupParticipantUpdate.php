<?php

namespace JsonApi\Routes\Community;

use Community\CommunityGroup;
use Community\CommunityGroupParticipant;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CommunityGroupParticipantUpdate extends JsonApiController
{
    use ValidationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        $participant = CommunityGroupParticipant::findByJsonApiId($args['id']);
        if (!$participant) {
            throw new RecordNotFoundException();
        }
        $json = $this->validate($request);

        $group = $participant->group;
        $user = $this->getUser($request);

        if (!Authority::canUpdateCommunityGroupParticipant($user, $participant, $group)) {
            throw new AuthorizationFailedException();
        }

        $participant = $this->updateParticipant($json, $participant);

        return $this->getContentResponse($participant);
    }

    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data')) {
            return 'Missing `data` member at document\'s top level.';
        }
        if (!self::arrayHas($json, 'data.id')) {
            return 'Document must have an `id`.';
        }

        $hasStatus = self::arrayHas($json, 'data.attributes.status');
        $hasRole = self::arrayHas($json, 'data.attributes.role');

        if (!$hasStatus && !$hasRole) {
            return 'At least one attribute ("status" or "role") must be provided for update.';
        }
    }

    private function updateParticipant($json, CommunityGroupParticipant $participant): CommunityGroupParticipant
    {
        $attributes = self::arrayGet($json, 'data.attributes');

        if (isset($attributes['status'])) {
            $participant->status = $attributes['status'];
        }

        if (isset($attributes['role'])) {
            $participant->role = $attributes['role'];
        }

        if ($participant->isDirty()) {
            $participant->store();
        }

        return $participant;
    }
}
