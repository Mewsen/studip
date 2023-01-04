<?php

namespace JsonApi\Schemas\Courseware;

use JsonApi\Schemas\SchemaProvider;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;
use Neomerx\JsonApi\Contracts\Schema\LinkInterface;

class CustomFile extends SchemaProvider
{
    const TYPE = 'courseware-custom-file';
    //const REL_CUSTOM_FILE = 'courseware-custom-file';

    /**
     * {@inheritdoc}
     */
    public function getId($resource): ?string
    {
        return $resource->getPayload()['id'];
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return $resource->getPayload();
    }

    /**
     * {@inheritdoc}
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {

        return [];
    }

    public function getSelfLink($resource): LinkInterface
    {
        $link = new Link(true, '/courseware-blocks/' . $resource->getBlockId()
            .'/custom-files', false);
        return $link;
    }

    /**
     * @inheritdoc
     */
    public function hasResourceMeta($resource): bool
    {
        return true;
    }

    public function getResourceMeta($resource)
    {
        return [
            'download-url' => $resource->getDownloadUrl()
        ];
    }
}
