<?php

namespace JsonApi\Schemas;

use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use JsonApi\Schemas\SchemaProvider;

class Datafield extends SchemaProvider
{
    const TYPE = 'datafield';

    public function getId($datafield): ?string
    {
        return $datafield->getId();
    }

    public function getAttributes($datafield, ContextInterface $context): iterable
    {
        return [
            'name' => (string) $datafield->name,
            'object_type' => $datafield->object_type,
            'object_class' => $datafield->object_class,
            'institut_id' => $datafield->institut_id,
            'priority' => $datafield->priority,
            'type' => $datafield->type,
            'typeparam' => $datafield->typeparam,
            'is_required' => (bool) $datafield->is_required,
            'default_value' => $datafield->default_value,
            'is_userfilter' => (bool) $datafield->is_userfilter,
            'mkdate' => date('c', $datafield->mkdate),
            'chdate' => date('c', $datafield->chdate)
        ];
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getRelationships($datafield, ContextInterface $context): iterable
    {
        return [];
    }
}
