<?php

namespace JsonApi\Schemas\Community;

use JsonApi\Schemas\SchemaProvider;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;

class CommunityGroupParticipant extends SchemaProvider
{
    public const TYPE = 'community-group-participants';

    const REL_USER = 'user';
    const REL_GROUP = 'group';

    public function getId($resource): ?string
    {
        return $resource->group_id . '_' . $resource->user_id;
    }

    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'role' => $resource->role,
            'status' => $resource->status,
            'full-name' => $resource->getFullName(),
            'mkdate' => date('c', $resource->mkdate),
            'chdate' => date('c', $resource->chdate),
        ];
    }

    public function getRelationships($resource, ContextInterface $context): iterable
    {
        return [
            self::REL_USER => [
                self::RELATIONSHIP_DATA => $resource->user,
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->getRelationshipRelatedLink($resource, self::REL_USER),
                ],
            ],
            self::REL_GROUP => [
                self::RELATIONSHIP_DATA => $resource->group,
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->getRelationshipRelatedLink($resource, self::REL_GROUP),
                ],
            ],
        ];
    }
}