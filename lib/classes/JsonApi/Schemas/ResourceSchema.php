<?php
namespace JsonApi\Schemas;

use Neomerx\JsonApi\Contracts\Schema\BaseLinkInterface;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Resource;

final class ResourceSchema extends SchemaProvider
{
    const TYPE = 'resources';

    const REL_CATEGORY = 'category';

    /**
     * @param Resource $resource
     */
    public function getId($resource): ?string
    {
        return $resource->id;
    }

    /**
     * @param Resource $resource
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'level' => (int) $resource->level,
            'name' => (string) $resource->name,
            'description' => (string) $resource->description,
            'requestable' => (bool) $resource->requestable,
            'lockable' => (bool) $resource->lockable,
            'sort_position' => (int) $resource->sort_position,

            'mkdate' => date('c', $resource->mkdate),
            'chdate' => date('c', $resource->chdate),
        ];
    }

    /**
     * @param Resource $resource
     */
    public function hasResourceMeta($resource): bool
    {
        return true;
    }

    /**
     * @param Resource $resource
     */
    public function getResourceMeta($resource)
    {
        return [
            'class' => $resource->class_name,
        ];
    }

    /**
     * @param Resource $resource
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        if ($context->getPosition()->getLevel() > 0) {
            return [];
        };

        $relationships = [];

        $relationships = $this->getCategoryRelationship(
            $relationships,
            $resource,
            $this->shouldInclude($context, self::REL_CATEGORY)
        );

        return $relationships;
    }

    private function getCategoryRelationship(array $relationships, $resource, bool $shouldInclude)
    {
        $relationships[self::REL_CATEGORY] = [
            self::RELATIONSHIP_LINKS => [
                BaseLinkInterface::RELATED => $this->getRelationshipRelatedLink($resource, self::REL_CATEGORY),
            ],
            self::RELATIONSHIP_DATA => $shouldInclude ? $resource->category : \ResourceCategory::build(['id' => $resource->category_id]),
        ];

        return $relationships;
    }
}
