<?php

namespace JsonApi\Schemas\Forum;

use JsonApi\Schemas\SchemaProvider;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;

class Topic extends SchemaProvider
{
    const TYPE = 'forum-topics';
    const REL_CATEGORY = 'category';
    const REL_DISCUSSION = 'discussion';

    /**
     * @param \Forum\Topic $resource
     */
    public function getId($resource): ?string
    {
        return $resource->topic_id;
    }

    /**
     * @inheritdoc
     * @param \Forum\Topic $resource
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'name' => $resource->name,
            'description' => $resource->description,
            'position' => (int) $resource->position,
            'mkdate' => date('c', $resource->mkdate),
            'chdate' => date('c', $resource->chdate)
        ];
     }

    /**
     * @param \Forum\Topic $resource
     */
    public function hasResourceMeta($resource): bool
    {
        return true;
    }

    /**
     * @param \Forum\Topic $resource
     */
    public function getResourceMeta($resource)
    {
        $metaData = $resource->getMetaData();

        return [
            'discussions-count' => (int) $metaData['discussions_count'],
            'postings-count' => (int) $metaData['postings_count'],
            'user-read-index' => (int) $metaData['user_read_index'],
            'users-count' => (int) $metaData['users_count'],
            'recent-activity' => $metaData['recent_activity'] ? date('c', $metaData['recent_activity']) : '',
        ];
    }

    /**
     * @param \Forum\Topic $resource
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];
        $relationships = $this->addCategoryRelationship($relationships, $resource, $this->shouldInclude($context, self::REL_CATEGORY));
        $relationships = $this->addDiscussionsRelationship($relationships, $resource, $this->shouldInclude($context, self::REL_DISCUSSION));

        return $relationships;
    }

    private function addCategoryRelationship(array $relationships, \Forum\Topic $topic, bool $withCategory = false)
    {
        if ($withCategory) {
            $relationships[self::REL_CATEGORY] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->getRelationshipRelatedLink($topic, self::REL_CATEGORY)
                ],
                self::RELATIONSHIP_DATA => $topic->category
            ];
        }

        return $relationships;
    }

    private function addDiscussionsRelationship(array $relationships, \Forum\Topic $topic, bool $withDiscussions = false)
    {
        if ($withDiscussions) {
            $relationships[self::REL_DISCUSSION] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->getRelationshipRelatedLink($topic, self::REL_DISCUSSION)
                ],
                self::RELATIONSHIP_DATA => $topic->dicussions
            ];
        }

        return $relationships;
    }
}
