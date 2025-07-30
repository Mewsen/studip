<?php

namespace JsonApi\Schemas\Forum;

use JsonApi\Schemas\SchemaProvider;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;

class PostingReaction extends SchemaProvider
{
    const TYPE = 'forum-posting-reactions';
    const REL_POSTING = 'posting';
    const REL_USER = 'user';

    public function getId($postingReaction): ?string
    {
        return $postingReaction->id;
    }

    public function getAttributes($postingReaction, ContextInterface $context): iterable
    {
        return [
            'emoji' => $postingReaction->emoji,
            'mkdate' => date('c', $postingReaction->mkdate),
            'chdate' => date('c', $postingReaction->chdate)
        ];
    }


    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getRelationships($postingReaction, ContextInterface $context): iterable
    {
        $relationships = [];

        $relationships = $this->addPostingRelationship($relationships, $postingReaction, $this->shouldInclude($context, self::REL_POSTING));
        $relationships = $this->addUserRelationship($relationships, $postingReaction, $this->shouldInclude($context, self::REL_USER));

        return $relationships;
    }


    private function addPostingRelationship(array $relationships, $postingReaction, bool $withPosting = false)
    {
        if ($withPosting) {
            $relationships[self::REL_POSTING] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->createLinkToResource($postingReaction->posting)
                ],
                self::RELATIONSHIP_DATA => $postingReaction->posting
            ];
        }

        return $relationships;
    }

    private function addUserRelationship(array $relationships, $postingReaction, bool $withUser = false)
    {
        $user = $postingReaction->user;
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
}
