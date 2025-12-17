<?php

namespace JsonApi\Schemas\Community;

use JsonApi\Schemas\SchemaProvider;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;

class CommunityGroupPinboardItem extends SchemaProvider
{
    public const TYPE = 'community-group-pinboard-items';

    const REL_OWNER = 'owner';
    const REL_FILE = 'file';

    public function getId($resource): ?string
    {
        return (string) $resource->id;
    }

    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'item-type' => $resource->item_type,
            'payload'   => (array) $resource->payload,
            'mkdate'    => date('c', $resource->mkdate),
            'chdate'    => date('c', $resource->chdate),
        ];
    }

    public function getRelationships($resource, ContextInterface $context): iterable
    {
        return [
            self::REL_OWNER => [
                self::RELATIONSHIP_DATA => $resource->owner,
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->getRelationshipRelatedLink($resource, self::REL_OWNER),
                ],
            ],
            self::REL_FILE => [
                self::RELATIONSHIP_DATA => $resource->file,
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->getRelationshipRelatedLink($resource, self::REL_FILE),
                ],
            ],
        ];
    }
}