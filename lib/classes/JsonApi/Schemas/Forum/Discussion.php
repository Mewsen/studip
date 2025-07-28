<?php

namespace JsonApi\Schemas\Forum;

use JsonApi\Schemas\SchemaProvider;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;

class Discussion extends SchemaProvider
{
    const TYPE = 'forum-discussions';
    const REL_POSTINGS = 'postings';
    const REL_TOPIC = 'topic';
    const REL_CATEGORY = 'category';
    const REL_USER = 'user';
    const REL_DISCUSSION_TYPE = 'discussion-type';
    const REL_MEMBERS = 'members';
    const REL_TAGS = 'tags';

    public function getId($discussion): ?string
    {
        return $discussion->discussion_id;
    }

    public function getAttributes($discussion, ContextInterface $context): iterable
    {
        return [
            'title' => $discussion->title,
            'closed-at' => $discussion->closed_at ? date('c', $discussion->closed_at) : null,
            'sticky' => (bool) $discussion->sticky,
            'view-count' => (int) $discussion->view_count,
            'mkdate' => date('c', $discussion->mkdate),
            'chdate' => date('c', $discussion->chdate)
        ];
    }

    public function hasResourceMeta($discussion): bool
    {
        return true;
    }

    public function getResourceMeta($discussion)
    {
        $metaData = $discussion->getMetaData();

        return [
            'postings-count' => (int) $metaData['postings_count'],
            'recent-postings-count' => (int) $metaData['recent_postings_count'],
            'user-read-index' => (int) $metaData['user_read_index'],
            'recent-activity' => $metaData['recent_activity'] ? date('c', $metaData['recent_activity']) : ''
        ];
    }

    public function getRelationships($discussion, ContextInterface $context): iterable
    {
        $relationships = [];

        $relationships = $this->addPostingsRelationship($relationships, $discussion, $this->shouldInclude($context, self::REL_POSTINGS));
        $relationships = $this->addTopicRelationship($relationships, $discussion, $this->shouldInclude($context, self::REL_TOPIC));
        $relationships = $this->addCategoryRelationship($relationships, $discussion, $this->shouldInclude($context, self::REL_CATEGORY));
        $relationships = $this->addUserRelationship($relationships, $discussion, $this->shouldInclude($context, self::REL_USER));
        $relationships = $this->addDiscussionTypeRelationship($relationships, $discussion, $this->shouldInclude($context, self::REL_DISCUSSION_TYPE));
        $relationships = $this->addMembersRelationship($relationships, $discussion, $this->shouldInclude($context, self::REL_MEMBERS));
        $relationships = $this->addTagsRelationship($relationships, $discussion, $this->shouldInclude($context, self::REL_TAGS));

        return $relationships;
    }

    private function addPostingsRelationship(array $relationships, $discussion, bool $withPostings = false)
    {
        if ($withPostings) {
            $relationships[self::REL_POSTINGS] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->getRelationshipRelatedLink($discussion, self::REL_POSTINGS)
                ],
                self::RELATIONSHIP_DATA => $discussion->postings
            ];
        }

        return $relationships;
    }

    private function addTopicRelationship(array $relationships, $discussion, bool $withTopic = false)
    {
        if ($withTopic) {
            $relationships[self::REL_TOPIC] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->createLinkToResource($discussion->topic)
                ],
                self::RELATIONSHIP_DATA => $discussion->topic
            ];
        }

        return $relationships;
    }

    private function addCategoryRelationship(array $relationships, $discussion, bool $withCategory = false)
    {
        $category = $discussion->category;
        if ($withCategory && $category) {
            $relationships[self::REL_CATEGORY] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->createLinkToResource($category)
                ],
                self::RELATIONSHIP_DATA => $category
            ];
        }

        return $relationships;
    }

    private function addUserRelationship(array $relationships, $discussion, bool $withUser = false)
    {
        $user = $discussion->user;
        if ($withUser && $user) {
            $relationships[self::REL_USER] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->createLinkToResource($user)
                ],
                self::RELATIONSHIP_DATA => $user
            ];
        }

        return $relationships;
    }

    private function addDiscussionTypeRelationship(array $relationships, $discussion, bool $withDiscussionType = false)
    {
        $discussionType = $discussion->discussion_type;

        if ($withDiscussionType && $discussionType) {
            $relationships[self::REL_DISCUSSION_TYPE] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->createLinkToResource($discussionType)
                ],
                self::RELATIONSHIP_DATA => $discussionType
            ];
        }

        return $relationships;
    }

    private function addMembersRelationship(array $relationships, $discussion, bool $withMembers = false)
    {
        if ($withMembers) {
            $relationships[self::REL_MEMBERS] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->getRelationshipRelatedLink($discussion, self::REL_MEMBERS)
                ],
                self::RELATIONSHIP_DATA => $discussion->members
            ];
        }

        return $relationships;
    }

    private function addTagsRelationship(array $relationships, $discussion, bool $withTags = false)
    {
        if ($withTags) {
            $relationships[self::REL_TAGS] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->getRelationshipRelatedLink($discussion, self::REL_TAGS)
                ],
                self::RELATIONSHIP_DATA => $discussion->tags
            ];
        }

        return $relationships;
    }
}
