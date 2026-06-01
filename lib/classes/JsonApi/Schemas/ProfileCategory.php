<?php

namespace JsonApi\Schemas;

use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;

class ProfileCategory extends SchemaProvider
{
    const TYPE = 'profile-categories';

    const REL_USER = 'user';

    protected array $allowedIncludes = [
        self::REL_USER,
    ];

    /**
     * @param \Kategorie $resource
     */
    public function getId($resource): ?string
    {
        return $resource->id;
    }

    /**
     * @param \Kategorie $resource
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        $attributes = [
            'title' => $resource->name,
            'content' => $resource->content,
            'priority' => (int) $resource->priority,
            'mkdate' => date('c', $resource->mkdate),
            'chdate' => date('c', $resource->chdate)
        ];

        return $attributes;
    }

    /**
     * @param \Kategorie $resource
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [
            self::REL_USER => [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->createLinkToResource($resource->user),
                ],
                self::RELATIONSHIP_DATA => $resource->user,
            ],
        ];

        return $relationships;
    }
}
