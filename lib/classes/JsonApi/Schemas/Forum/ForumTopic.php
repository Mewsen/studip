<?php

namespace JsonApi\Schemas\Forum;

use JsonApi\Schemas\SchemaProvider;
use JsonApi\Schemas\Studip;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;

class ForumTopic extends SchemaProvider
{
    const TYPE = 'forum-topics';
    const REL_CATEGORY = 'category';
    const REL_DISCUSSION = 'discussion';

    public function getId($topic): ?string
    {
        return $topic->topic_id;
    }

    /**
     * @inheritdoc
     *
     * @param \Forum\ForumTopic $topic
     */
    public function getAttributes($topic, ContextInterface $context): iterable
    {
        return [
            'name' => $topic->name,
            'description' => $topic->description,
            'position' => (int) $topic->position,
            'mkdate' => date('c', $topic->mkdate),
            'chdate' => date('c', $topic->chdate)
        ];
     }

    /**
     * @inheritdoc
     *
     * @param \Forum\ForumTopic $topic
     */
    public function hasResourceMeta($topic): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     *
     * @param \Forum\ForumTopic $topic
     */
    public function getResourceMeta($topic)
    {
        $metaData = $topic->getMetaData();

        return [
            'discussions-count' => (int) $metaData['discussions_count'],
            'postings-count' => (int) $metaData['postings_count'],
            'user-read-index' => (int) $metaData['user_read_index'],
            'users-count' => (int) $metaData['users_count'],
            'recent-activity' => $metaData['recent_activity'] ? date('c', $metaData['recent_activity']) : '',
        ];
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param \Forum\ForumTopic $topic
     */
    public function getRelationships($topic, ContextInterface $context): iterable
    {
        $relationships = [];
        $relationships = $this->addCategoryRelationship($relationships, $topic, $this->shouldInclude($context, self::REL_CATEGORY));
        $relationships = $this->addDiscussionsRelationship($relationships, $topic, $this->shouldInclude($context, self::REL_DISCUSSION));

        return $relationships;
    }

    private function addCategoryRelationship($relationships, $topic, $withCategory = false)
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

    private function addDiscussionsRelationship($relationships, $topic, $withDiscussions = false)
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
