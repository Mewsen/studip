<?php
namespace JsonApi\Schemas;

use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use ResourceCategory;

final class ResourceCategorySchema extends SchemaProvider
{
    const TYPE = 'resource_categories';

    /**
     * @param ResourceCategory $resource
     */
    public function getId($resource): ?string
    {
        return $resource->id;
    }

    /**
     * @param ResourceCategory $resource
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'name' => (string) $resource->name,
            'description' => (string) $resource->description,
            'system' => (bool) $resource->system,
            'class_name' => (string) $resource->class_name,

            'mkdate' => date('c', $resource->mkdate),
            'chdate' => date('c', $resource->chdate),
        ];
    }

    /**
     * @param ResourceCategory $resource
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        if ($context->getPosition()->getLevel() > 0) {
            return [];
        };

        $relationships = [];

        return $relationships;
    }
}
