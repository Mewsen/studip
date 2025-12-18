<?php

namespace JsonApi\Schemas\Community;

use JsonApi\Schemas\SchemaProvider;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;

class CommunityGroup extends SchemaProvider
{
    /**
     * @inheritdoc
     */
    public const TYPE = 'community-groups';

    /**
     * @var string the creator relationship flag.
     */
    const REL_CREATOR = 'creator';

    /**
     * @var string the participants relationship flag.
     */
    const REL_PARTICIPANTS = 'participants';

    /**
     * @var string the pinboard items relationship flag.
     */
    const REL_PINBOARD_ITEMS = 'pinboard-items';

    /**
     * @inheritdoc
     */
    public function getId($resource): ?string
    {
        return $resource->getId();
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function hasResourceMeta($resource): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getResourceMeta($resource)
    {
        return [

        ];
    }

    /**
     * @inheritdoc
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];

        // Relationship to the Creator (User Model)
        $creator = $resource->creator;
        $relationships[self::REL_CREATOR] = [
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->createLinkToResource($creator),
            ],
            self::RELATIONSHIP_DATA => $creator,
        ];

        // Relationship to Participants
        $relationships[self::REL_PARTICIPANTS] = [
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->getRelationshipRelatedLink($resource, self::REL_PARTICIPANTS),
            ],
            self::RELATIONSHIP_DATA => $resource->participants ?? [],
        ];

        // Relationship to Pinboard Items
        $relationships[self::REL_PINBOARD_ITEMS] = [
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->getRelationshipRelatedLink($resource, self::REL_PINBOARD_ITEMS),
            ],
            self::RELATIONSHIP_DATA => $resource->pinboard_items ?? [],
        ];

        return $relationships;
    }
}
