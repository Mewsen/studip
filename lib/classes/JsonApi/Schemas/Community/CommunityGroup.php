<?php

namespace JsonApi\Schemas\Community;

use JsonApi\Schemas\SchemaProvider;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;

class CommunityGroup extends SchemaProvider
{
    public const TYPE = 'community-groups';

    const REL_CREATOR = 'creator';

    const REL_PARTICIPANTS = 'participants';

    const REL_PINBOARD_ITEMS = 'pinboard-items';

    public function getId($resource): ?string
    {
        return $resource->getId();
    }

    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'name' => $resource->name,
            'description' => $resource->description,
            'is-private' => (bool) $resource->is_private,
            'status' => $resource->status,
            'member-count' => $resource->getMemberCount(),
            'mkdate' => date('c', $resource->mkdate),
            'chdate' => date('c', $resource->chdate),
        ];
    }

    public function hasResourceMeta($resource): bool
    {
        return false;
    }

    public function getResourceMeta($resource)
    {
        return [

        ];
    }

    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];

        // Relationship to the Creator (User Model)
        $relationships[self::REL_CREATOR] = [
            self::RELATIONSHIP_DATA => $resource->creator,
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->getRelationshipRelatedLink($resource, 'creator'),
            ],
        ];

        // Relationship to Participants
        $relationships[self::REL_PARTICIPANTS] = [
            self::RELATIONSHIP_DATA => $resource->participants,
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->getRelationshipRelatedLink($resource, 'participants'),
            ],
        ];

        // Relationship to Pinboard Items
        $relationships[self::REL_PINBOARD_ITEMS] = [
            self::RELATIONSHIP_DATA => $resource->pinboard_items,
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->getRelationshipRelatedLink($resource, 'pinboard-items'),
            ],
        ];

        return $relationships;
    }
}