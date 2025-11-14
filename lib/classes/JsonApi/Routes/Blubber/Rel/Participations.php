<?php

namespace JsonApi\Routes\Blubber\Rel;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\Blubber\Authority;
use JsonApi\Routes\RelationshipsController;
use JsonApi\Routes\Users\Authority as UsersAuthority;
use Psr\Http\Message\ServerRequestInterface as Request;

class Participations extends RelationshipsController
{
    protected $allowedPagingParameters = ['offset', 'limit'];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function fetchRelationship(Request $request, $related)
    {
        $participations = $related->participations;
        $total = count($participations);
        list($offset, $limit) = $this->getOffsetAndLimit();

        return $this->getPaginatedIdentifiersResponse(
            $participations->limit($offset, $limit)->pluck('user'),
            $total,
            $this->getRelationshipLinks($related)
        );
    }

    protected function addToRelationship(Request $request, $related)
    {
        $json = $this->validate($request);

        foreach ($this->validateParticipations($this->getUser($request), $json) as $participation) {
            if (!\BlubberParticipation::countBySQL('thread_id = ? AND user_id = ?', [$related->id, $participation->id])) {
                \BlubberParticipation::create(['thread_id' => $related->id, 'user_id' => $participation->id]);
            }
        }

        return $this->getCodeResponse(204);
    }

    protected function removeFromRelationship(Request $request, $related)
    {
        $json = $this->validate($request);
        $participations = $this->validateParticipations($user = $this->getUser($request), $json);

        $notMe = array_filter($participations, function (\User $participation) use ($user) {
            return $participation->id !== $user->id;
        });

        if (count($notMe)) {
            throw new AuthorizationFailedException('Users cannot remove other participated users.');
        }

        $this->removeParticipations($related, $participations);

        return $this->getCodeResponse(204);
    }

    protected function findRelated(array $args)
    {
        if (!$thread = \BlubberThread::find($args['id'])) {
            throw new RecordNotFoundException();
        }

        return $thread;
    }

    protected function authorize(Request $request, $resource)
    {
        return Authority::canCreateComment($this->getUser($request), $resource);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getRelationshipSelfLink($resource, $schema, $userData)
    {
        return $schema->getRelationshipSelfLink($resource, 'participations');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getRelationshipRelatedLink($resource, $schema, $userData)
    {
        return $schema->getRelationshipRelatedLink($resource, 'participations');
    }

    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data')) {
            return 'Missing `data` member at document´s top level.';
        }

        $data = self::arrayGet($json, 'data');

        if (!is_array($data)) {
            return 'Document´s ´data´ must be an array.';
        }

        foreach ($data as $item) {
            if (\JsonApi\Schemas\User::TYPE !== self::arrayGet($item, 'type')) {
                return 'Wrong `type` in document´s `data`.';
            }

            if (!self::arrayGet($item, 'id')) {
                return 'Missing `id` of document´s `data`.';
            }
        }

        if (self::arrayHas($json, 'data.attributes')) {
            return 'Document must not have `attributes`.';
        }
    }

    private function validateParticipations(\User $user, $json)
    {
        $participations = [];

        foreach (self::arrayGet($json, 'data') as $participationResource) {
            if (!$participation = \User::find($participationResource['id'])) {
                throw new RecordNotFoundException();
            }

            if (!UsersAuthority::canShowUser($user, $participation)) {
                throw new RecordNotFoundException();
            }

            $participations[] = $participation;
        }

        return $participations;
    }

    private function removeParticipations(\BlubberThread $thread, array $users)
    {
        foreach ($users as $user) {
            \BlubberParticipation::deleteBySQL('thread_id = ? AND user_id = ?', [$thread->id, $user->id]);
        }
    }
}
