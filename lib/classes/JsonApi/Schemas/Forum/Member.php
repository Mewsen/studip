<?php
namespace JsonApi\Schemas\Forum;

use JsonApi\Schemas\SchemaProvider;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;

class Member extends SchemaProvider
{
    const TYPE = 'forum-members';

    /**
     * @param \Forum\DTO\Member $resource
     */
    public function getId($resource): ?string
    {
        return $resource->id;
    }

    /**
     * @param \Forum\DTO\Member $resource
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'username' => $resource->username,
            'name' => $resource->name,
            'role' => $resource->role,
            'avatar_url' => $resource->avatar_url
        ];
    }

    /**
     * @param \Forum\DTO\Member $resource
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        return [];
    }
}
