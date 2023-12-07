<?php
namespace JsonApi\Schemas;

use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use ResourceProperty;

final class ResourcePropertySchema extends SchemaProvider
{
    /**
     * @param ResourceProperty $resource
     */
    public function getId($resource): ?string
    {
        return $resource->id;
    }

    /**
     * @param ResourceProperty $resource
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'state' => $resource->state,

            'mkdate' => date('c', $resource->mkdate),
            'chdate' => date('c', $resource->chdate),
        ];
    }

    /**
     * @param ResourceProperty $resource
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        return [];
    }
}
