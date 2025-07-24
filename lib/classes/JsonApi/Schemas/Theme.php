<?php

namespace JsonApi\Schemas;

use Neomerx\JsonApi\Contracts\Schema\ContextInterface;

class Theme extends SchemaProvider
{
    public const TYPE = 'studip-themes';

    /**
     * @param \Theme $resource
     */
    public function getId($resource): ?string
    {
        return $resource->getId();
    }

    /**
     * @param \Theme $resource
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'name'               => $resource->name,
            'active'             => (bool) $resource->active,
            'origin'             => $resource->origin,
            'studip_min_version' => $resource->studip_min_version,
            'studip_max_version' => $resource->studip_max_version,
            'author'             => $resource->author,
            'description'        => $resource->description,
            'type'               => $resource->type,
            'values'             => $resource->values->getArrayCopy() ?: null,

            'mkdate' => date('c', $resource->mkdate),
            'chdate' => date('c', $resource->chdate),
        ];
    }

    /**
     * @param \Theme $resource
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        return [];
    }

    /**
     * @param \Theme $resource
     */
    public function hasResourceMeta($resource): bool
    {
        return true;
    }

    /**
     * @param \Theme $resource
     */
    public function getResourceMeta($resource): iterable
    {
        return [
            'colorKeyCategories' => $resource->getColorKeyCategories(),
        ];
    }
}
