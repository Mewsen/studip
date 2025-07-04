<?php
namespace JsonApi\Schemas\Forum;

use JsonApi\Schemas\SchemaProvider;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;

class ForumDiscussionType extends SchemaProvider
{
    const TYPE = 'forum-discussion-types';

    const REL_DISCUSSIONS = 'discussions';

    public function getId($discussionType): ?string
    {
        return $discussionType->type_id;
    }

    public function getAttributes($discussionType, ContextInterface $context): iterable
    {
        return [
            'name' => $discussionType->name,
            'icon' => $discussionType->icon,
            'mkdate' => date('c', $discussionType->mkdate),
            'chdate' => date('c', $discussionType->chdate),
        ];
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getRelationships($discussionType, ContextInterface $context): iterable
    {
        $relationships = [];

        $relationships = $this->addDiscussionsRelationship($relationships, $discussionType, $this->shouldInclude($context, self::REL_DISCUSSIONS));

        return $relationships;
    }

    private function addDiscussionsRelationship($relationships, $discussionType, $withDiscussions = false)
    {
        if ($withDiscussions) {
            $relationships[self::REL_DISCUSSIONS] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->getRelationshipRelatedLink($discussionType, self::REL_DISCUSSIONS)
                ],
                self::RELATIONSHIP_DATA => $discussionType->discussions
            ];
        }

        return $relationships;
    }
}
