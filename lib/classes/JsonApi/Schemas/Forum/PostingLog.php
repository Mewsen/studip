<?php

namespace JsonApi\Schemas\Forum;

use JsonApi\Schemas\SchemaProvider;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;

class PostingLog extends SchemaProvider
{
    const TYPE = 'forum-posting-logs';
    const REL_USER = 'user';
    const REL_POSTING = 'posting';

    /**
     * @param \Forum\PostingLog $resource
     */
    public function getId($resource): ?string
    {
        return $resource->id;
    }

    /**
     * @param \Forum\PostingLog $resource
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'action' => $resource->action,
            'mkdate' => date('c', $resource->mkdate)
        ];
    }

    /**
     * @param \Forum\PostingLog $resource
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];
        $relationships = $this->addPostingRelationship($relationships, $resource, $this->shouldInclude($context, self::REL_POSTING));
        $relationships = $this->addUserRelationship($relationships, $resource, $this->shouldInclude($context, self::REL_USER));

        return $relationships;
    }


    private function addPostingRelationship(array $relationships, \Forum\PostingLog $postingLog, bool $withPosting = false)
    {
        $posting = $postingLog->posting;

        if ($withPosting && $posting) {
            $relationships[self::REL_POSTING] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->createLinkToResource($posting)
                ],
                self::RELATIONSHIP_DATA => $posting
            ];
        }

        return $relationships;
    }

    private function addUserRelationship(array $relationships, \Forum\PostingLog $postingLog, bool $withUser = false)
    {
        $user = $postingLog->user;

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
