<?php
namespace JsonApi\Schemas\Forum;

use JsonApi\Schemas\SchemaProvider;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;

class ForumTag extends SchemaProvider
{
    const TYPE = 'forum-tags';

    public function getId($tag): ?string
    {
        return $tag->id;
    }

    public function getAttributes($tag, ContextInterface $context): iterable
    {
        return [
            'name' => $tag->name
        ];
    }

    public function getRelationships($resource, ContextInterface $context): iterable
    {
        return [];
    }
}
