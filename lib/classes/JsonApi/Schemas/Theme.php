<?php

namespace JsonApi\Schemas;

use JsonApi\Schemas\SchemaProvider;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;
use Neomerx\JsonApi\Contracts\Schema\LinkInterface;

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
            'name' => $resource['name'],
            'active' => (bool)$resource['active'],
            'origin' => $resource['origin'],
            'studip_min_version' => $resource['studip_min_version'],
            'studip_max_version' => $resource['studip_max_version'],
            'author' => $resource['author'],
            'description' => $resource['description'],
            'type' => $resource['type'],
            'values' => empty($resource['values']) ? null : json_decode($resource['values']),

            'mkdate' => date('c', $resource['mkdate']),
            'chdate' => date('c', $resource['chdate']),
        ];
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        return [];
    }

    public function hasResourceMeta($resource): bool
    {
        return true;
    }

    public function getResourceMeta($resource): iterable
    {
        return [
            'colorKeyCategories' => $resource->getColorKeyCategories(),
        ];
    }
}