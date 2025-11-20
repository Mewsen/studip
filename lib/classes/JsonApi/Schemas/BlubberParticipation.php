<?php

namespace JsonApi\Schemas;

use JsonApi\Errors\InternalServerError;
use Neomerx\JsonApi\Schema\Link;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;

class BlubberParticipation extends SchemaProvider
{
    const TYPE = 'blubber-participations';
    const REL_THREAD = 'thread';
    const REL_USER = 'user';

    public function getId($resource): ?string
    {
        return $resource->id;
    }

    public function getAttributes($resource, ContextInterface $context): iterable
    {
        $attributes = [
            'thread_id' => $resource['thread_id'],
            'user_id' => $resource['user_id'],
            'external_contact' => (bool)$resource['external_contact'],
            'mkdate' => date('c', $resource['mkdate']),
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
        $relationships = $this->getUserRelationship(
            $relationships,
            $resource,
            $this->shouldInclude($context, self::REL_USER)
        );

        $relationships = $this->getThreadRelationship(
            $relationships,
            $resource,
            $this->shouldInclude($context, self::REL_THREAD)
        );

        return $relationships;
    }

    // #### PRIVATE HELPERS ####

    private function getUserRelationship($relationships, $resource, $includeData)
    {
        if (!$resource['external_contact'] && $resource['user_id']) {
            $userId = $resource['user_id'];
            $related = $includeData ? \User::find($userId) : \User::build(['id' => $userId], false);
            $relationships[self::REL_USER] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->createLinkToResource($related),
                ],
                self::RELATIONSHIP_DATA => $related,
            ];
        } else {
            $relationships[self::REL_USER] = [
                self::RELATIONSHIP_DATA => null,
            ];
        }

        return $relationships;
    }

    private function getThreadRelationship($relationships, $resource, $includeData)
    {
        $thread = $resource->thread ?? null;
        if (!$thread) {
            return $relationships;
        }

        $relationships[self::REL_THREAD] = [
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->createLinkToResource($thread),
            ],
            self::RELATIONSHIP_DATA => $includeData ? $thread : null,
        ];

        return $relationships;
    }
}
