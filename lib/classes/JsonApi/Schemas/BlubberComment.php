<?php

namespace JsonApi\Schemas;

use Neomerx\JsonApi\Schema\Link;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;

class BlubberComment extends SchemaProvider
{
    const TYPE = 'blubber-comments';
    const REL_AUTHOR = 'author';
    const REL_PARTICIPATIONS = 'participations';
    const REL_THREAD = 'thread';

    public function getId($resource): ?string
    {
        return $resource->id;
    }

    public function getAttributes($resource, ContextInterface $context): iterable
    {
        $userId = $this->currentUser->id;

        $attributes = [
            # `network` VARCHAR(64) COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
            'content' => $resource['content'],
            'content-html' => blubberReady($resource['content']),

            'is-writable' => $resource->isWritable($userId),

            'mkdate' => date('c', $resource['mkdate']),
            'chdate' => date('c', $resource['chdate']),
        ];

        return $attributes;
    }

    /**
     * In dieser Methode können Relationships zu anderen Objekten
     * spezifiziert werden.
     * {@inheritdoc}
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];
        $relationships = $this->getAuthorRelationship($relationships, $resource, $this->shouldInclude($context, self::REL_AUTHOR));

        $isPrimary = $context->getPosition()->getLevel() === 0;
        if (!$isPrimary) {
            return $relationships;
        }

        $relationships = $this->getParticipationsRelationship($relationships, $resource, $this->shouldInclude($context, self::REL_PARTICIPATIONS));
        $relationships = $this->getThreadRelationship($relationships, $resource, $this->shouldInclude($context, self::REL_THREAD));

        return $relationships;
    }

    // #### PRIVATE HELPERS ####

    private function getAuthorRelationship($relationships, $resource, $includeData)
    {
        if (!$resource['external_contact']) {
            $userId = $resource['user_id'];

            $user = \User::find($userId);
            if ($user) {
                $data = $includeData ? $user : \User::build(['id' => $userId], false);
                $relationships[self::REL_AUTHOR] = [
                    self::RELATIONSHIP_DATA => $data,
                    self::RELATIONSHIP_LINKS => [
                        Link::RELATED => $this->createLinkToResource($data),
                    ]
                ];
            } else {
                $relationships[self::REL_AUTHOR] = [ self::RELATIONSHIP_DATA => null ];
            }
        }

        return $relationships;
    }

    private function getParticipationsRelationship(array $relationships, \BlubberComment $resource, $includeData)
    {
        if ($includeData) {
            $relatedUsers = $resource->participations->pluck('user');
        } else {
            $relatedUsers = array_map(function ($participation) {
                return \User::build(['user_id' => $participation->user_id], false);
            }, \BlubberParticipation::findBySQL('thread_id = ?', [$resource->id]));
        }

        $relationships[self::REL_PARTICIPATIONS] = [
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->getRelationshipRelatedLink($resource, self::REL_PARTICIPATIONS),
            ],
            self::RELATIONSHIP_DATA => $relatedUsers,
        ];

        return $relationships;
    }

    private function getThreadRelationship(array $relationships, \BlubberComment $resource, $includeData)
    {
        if ($includeData) {
            $related = $resource->thread;
        } else {
            $related = \BlubberThread::build(['id' => $resource->thread_id], false);
        }

        $relationships[self::REL_THREAD] = [
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->createLinkToResource($related),
            ],
            self::RELATIONSHIP_DATA => $related,
        ];

        return $relationships;
    }
}
