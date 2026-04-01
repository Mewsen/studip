<?php

namespace JsonApi\Schemas;

use Neomerx\JsonApi\Contracts\Schema\ContextInterface;

class Datafield extends SchemaProvider
{
    const TYPE = 'datafields';

    /**
     * @param \DataField $datafield
     */
    public function getId($datafield): ?string
    {
        return $datafield->id;
    }

    /**
     * @param \DataField $datafield
     */
    public function getAttributes($datafield, ContextInterface $context): iterable
    {
        return [
            'name' => (string) $datafield->name,
            'description' => $datafield->description,
            'object-type' => $datafield->object_type,
            'object-class' => $datafield->object_class,
            'priority' => (int) $datafield->priority,
            'type' => $datafield->type,
            'type-param' => $datafield->typeparam,
            'default-value' => $datafield->default_value,
            'required' => (bool) $datafield->is_required,
            'userfilter' => (bool) $datafield->is_userfilter,
            'mkdate' => date('c', $datafield->mkdate),
            'chdate' => date('c', $datafield->chdate)
        ];
    }

    /**
     * @param \DataField $datafield
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getRelationships($datafield, ContextInterface $context): iterable
    {
        return [];
    }
}
