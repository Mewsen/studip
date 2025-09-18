<?php
namespace JsonApi\Schemas\Forum;

use JsonApi\Schemas\SchemaProvider;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;

class Tag extends SchemaProvider
{
    const TYPE = 'forum-tags';

    /**
     * @param \Forum\DTO\Tag $resource
     */
    public function getId($resource): ?string
    {
        return $resource->id;
    }

    /**
     * @param \Forum\DTO\Tag $resource
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'name' => $resource->name
        ];
    }

    /**
     * @param \Forum\DTO\Tag $resource
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        return [];
    }
}
