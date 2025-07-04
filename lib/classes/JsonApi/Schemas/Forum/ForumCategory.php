<?php
namespace JsonApi\Schemas\Forum;

use JsonApi\Schemas\SchemaProvider;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;

class ForumCategory extends SchemaProvider
{
    const TYPE = 'forum-categories';
    const REL_TOPICS = 'topics';

    public function getId($category): ?string
    {
        return $category->id;
    }

    public function getAttributes($category, ContextInterface $context): iterable
    {
        return [
            'name' => $category->name,
            'description' => $category->description,
            'color' => $category->color,
            'position' => (int) $category->position,
            'mkdate' => date('c', $category->mkdate),
            'chdate' => date('c', $category->chdate)
        ];
    }

    public function hasResourceMeta($category): bool
    {
        return true;
    }

    public function getResourceMeta($category)
    {
        $metaData = $category->getMetaData();

        return [
            'topics-count' => (int) $metaData['topics_count'],
            'discussions-count' => (int) $metaData['discussions_count'],
            'postings-count' => (int) $metaData['postings_count'],
            'recent-postings-count' => (int) $metaData['recent_postings_count'],
            'user-read-index' => (int) $metaData['user_read_index'],
            'users-count' => (int) $metaData['users_count'],
            'recent-activity' => $metaData['recent_activity'] ? date('c', $metaData['recent_activity']) : '',
        ];
    }

    public function getRelationships($category, ContextInterface $context): iterable
    {
        $relationships = [];
        $relationships = $this->addTopicsRelationship($relationships, $category, $this->shouldInclude($context, self::REL_TOPICS));

        return $relationships;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function addTopicsRelationship($relationships, $category, $withTopics = false)
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
