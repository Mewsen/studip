<?php

namespace JsonApi\Schemas;

use Neomerx\JsonApi\Contracts\Schema\ContextInterface;

final class Tag extends SchemaProvider
{
    public const TYPE = 'tags';

    /**
     * @param \Tag $resource
     */
    public function getId($resource): ?string
    {
        return $resource->id;
    }

    /**
     * @param \Tag $resource
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'name'   => $resource->name,
            'active' => (bool) $resource->active,
            'mkdate' => date('c', $resource->mkdate),
            'chdate' => date('c', $resource->chdate),
        ];
    }

    /**
     * @param \Tag $resource
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        return [];
    }
}
