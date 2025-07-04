<?php
namespace JsonApi\Schemas\Forum;

use JsonApi\Schemas\SchemaProvider;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;

class ForumMember extends SchemaProvider
{
    const TYPE = 'forum-members';

    public function getId($member): ?string
    {
        return $member->id;
    }

    public function getAttributes($member, ContextInterface $context): iterable
    {
        return [
            'username' => $member->username,
            'name' => $member->name,
            'role' => $member->role,
            'avatar_url' => $member->avatar_url
        ];
    }

    public function getRelationships($resource, ContextInterface $context): iterable
    {
        return [];
    }
}
