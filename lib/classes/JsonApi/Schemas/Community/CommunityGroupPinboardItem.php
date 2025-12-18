<?php

namespace JsonApi\Schemas\Community;

use JsonApi\Schemas\SchemaProvider;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;

class CommunityGroupPinboardItem extends SchemaProvider
{
    /**
     * @inheritdoc
     */
    public const TYPE = 'community-group-pinboard-items';

    /**
     * @var string the owner relationship flag.
     */
    const REL_OWNER = 'owner';

    /**
     * @var string the file relationship flag.
     */
    const REL_FILE = 'file';

    /**
     * @inheritdoc
     */
    public function getId($resource): ?string
    {
        return (string) $resource->id;
    }

    /**
     * @inheritdoc
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'item-type' => $resource->item_type,
            'payload'   => $resource->payload->getArrayCopy(),
            'mkdate'    => date('c', $resource->mkdate),
            'chdate'    => date('c', $resource->chdate),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        return [
            self::REL_OWNER => [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->createLinkToResource($resource->owner),
                ],
                self::RELATIONSHIP_DATA => $resource->owner,
            ],
            self::REL_FILE => [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->getRelationshipRelatedLink($resource, self::REL_FILE),
                ],
                self::RELATIONSHIP_DATA => $resource->file,
            ],
        ];
    }
}
