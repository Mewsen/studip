<?php

namespace JsonApi\Schemas;

use Neomerx\JsonApi\Contracts\Schema\ContextInterface;

class AdmissionRule extends SchemaProvider
{
    const TYPE = 'admission-rules';

    public function getId($resource): ?string
    {
        return \get_class($resource) . '_' . $resource->getId();
    }

    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'type' => \get_class($resource),
            'name' => $resource->getName(),
            'description' => $resource->getDescription(),
            'payload' => $resource->getPayload(),
            'ruletext' => $resource->toString()
        ];
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        return [];
    }
}
