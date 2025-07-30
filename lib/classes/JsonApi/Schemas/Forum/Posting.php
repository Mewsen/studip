<?php

namespace JsonApi\Schemas\Forum;

use JsonApi\Schemas\SchemaProvider;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;

class Posting extends SchemaProvider
{
    const TYPE = 'forum-postings';
    const REL_AUTHOR = 'author';
    const REL_DISCUSSION = 'discussion';
    const REL_POSTING = 'posting';
    const REL_RANGE = 'range';
    const REL_REACTIONS = 'reactions';
    const REL_REACTIONS_USER = 'reactions.user';
    const REL_OPENGRAPH_URLS = 'opengraph-urls';

    public function getId($posting): ?string
    {
        return $posting->posting_id;
    }

    public function getAttributes($posting, ContextInterface $context): iterable
    {
        return [
            'content' => \Studip\Markup::markupToHtml($posting->content),
            'content-html' => formatReady($posting->content),
            'anonymous' => (bool) $posting->anonymous,
            'mkdate' => date('c', $posting->mkdate),
            'chdate' => date('c', $posting->chdate)
        ];
    }

    public function hasResourceMeta($posting): bool
    {
        return true;
    }

    public function getResourceMeta($posting)
    {
        return [
            self::REL_OPENGRAPH_URLS => array_map(fn($og) => [
                'url' => $og['url'],
                'is-opengraph' => (bool) $og['is_opengraph'],
                'title' => $og['title'],
                'description' => $og['description'],
                'image' => $og['image'],
            ], $posting->getOpenGraphURLs())
        ];
    }

    public function getRelationships($posting, ContextInterface $context): iterable
    {
        $relationships = [];
        $relationships = $this->addAuthorRelationship($relationships, $posting, $this->shouldInclude($context, self::REL_AUTHOR));
        $relationships = $this->addDiscussionRelationship($relationships, $posting, $this->shouldInclude($context, self::REL_DISCUSSION));
        $relationships = $this->addPostingRelationship($relationships, $posting, $this->shouldInclude($context, self::REL_POSTING));
        $relationships = $this->addReactionsRelationship($relationships, $posting, $this->shouldInclude($context, self::REL_REACTIONS));

        return $relationships;
    }

    private function addAuthorRelationship($relationships, $posting, $withAuthor = false)
    {
        $author = $posting->author;

        if ($withAuthor && $author) {
            $relationships[self::REL_AUTHOR] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->createLinkToResource($author)
                ],
                self::RELATIONSHIP_DATA => $author
            ];
        }

        return $relationships;
    }

    private function addDiscussionRelationship($relationships, $posting, $withDiscussion = false)
    {
        if ($withDiscussion) {
            $relationships[self::REL_DISCUSSION] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->createLinkToResource($posting->discussion)
                ],
                self::RELATIONSHIP_DATA => $posting->discussion
            ];
        }

        return $relationships;
    }

    private function addPostingRelationship($relationships, $posting, $withPosting = false)
    {
        $posting = $posting->posting;

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

    private function addReactionsRelationship($relationships, $posting, $withReactions = false)
    {
        if ($withReactions) {
            $relationships[self::REL_REACTIONS] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->getRelationshipRelatedLink($posting, self::REL_REACTIONS)
                ],
                self::RELATIONSHIP_DATA => $posting->reactions
            ];
        }

        return $relationships;
    }
}
