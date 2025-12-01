<?php

namespace JsonApi\Schemas;

use JsonApi\Schemas\SchemaProvider;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;

class UserContactGroup extends SchemaProvider
{
    public const TYPE = 'user-contact-groups';
    const REL_OWNER = 'owner';
    const REL_GROUP_USERS = 'group-users';

    public function getId($resource): ?string
    {
        return $resource->getId();
    }

    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'name' => $resource['name']
        ];
    }

    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];
        $relationships[self::REL_OWNER] = [
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->createLinkToResource($resource->owner),
            ],
            self::RELATIONSHIP_DATA => $resource->owner,
        ];
        $relationships[self::REL_GROUP_USERS] = [
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->getRelationshipSelfLink($resource, self::REL_GROUP_USERS),
            ],
        ];
        return $relationships;
    }
}
