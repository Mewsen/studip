<?php
namespace JsonApi\Schemas\Forum;

use JsonApi\Schemas\SchemaProvider;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;

class Category extends SchemaProvider
{
    const TYPE = 'forum-categories';
    const REL_TOPICS = 'topics';

    /**
     * @param \Forum\Category $resource
     */
    public function getId($resource): ?string
    {
        return $resource->id;
    }

    /**
     * @param \Forum\Category $resource
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'name' => $resource->name,
            'description' => $resource->description,
            'color' => $resource->color,
            'position' => (int) $resource->position,
            'mkdate' => date('c', $resource->mkdate),
            'chdate' => date('c', $resource->chdate)
        ];
    }

    /**
     * @param \Forum\Category $resource
     */
    public function hasResourceMeta($resource): bool
    {
        return true;
    }

    /**
     * @param \Forum\Category $resource
     */
    public function getResourceMeta($resource)
    {
        $metaData = $resource->getMetaData();

        return [
            'topics-count' => (int) $metaData['topics_count'],
            'discussions-count' => (int) $metaData['discussions_count'],
            'postings-count' => (int) $metaData['postings_count'],
            'user-read-index' => (int) $metaData['user_read_index'],
            'users-count' => (int) $metaData['users_count'],
            'recent-activity' => $metaData['recent_activity'] ? date('c', $metaData['recent_activity']) : '',
        ];
    }

    /**
     * @param \Forum\Category $resource
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];
        $relationships = $this->addTopicsRelationship($relationships, $resource, $this->shouldInclude($context, self::REL_TOPICS));

        return $relationships;
    }

    private function addTopicsRelationship(array $relationships, \Forum\Category $category, $withTopics = false)
    {
        if ($withTopics) {
            $relationships[self::REL_TOPICS] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->getRelationshipRelatedLink($category, self::REL_TOPICS)
                ],
                self::RELATIONSHIP_DATA => $category->topics
            ];
        }

        return $relationships;
    }
}
