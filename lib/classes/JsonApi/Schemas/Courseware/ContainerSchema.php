<?php

namespace JsonApi\Schemas\Courseware;

use JsonApi\Schemas\SormSchemaProvider;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;

class ContainerSchema extends SormSchemaProvider
{
    const TYPE = 'courseware-containers';

    const REL_BLOCKS = 'blocks';
    const REL_OWNER = 'owner';
    const REL_EDITOR = 'editor';
    const REL_EDITBLOCKER = 'edit-blocker';
    const REL_STRUCTURAL_ELEMENT = 'structural-element';


    /**
     * @param \Courseware\Container $resource
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        $attributes = parent::getAttributes($resource, $context);

        return $attributes;
    }
}
