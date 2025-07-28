<?php
namespace JsonApi\Routes\Forum;

use Forum\Discussion;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;
use Forum\PostingRead;

class DiscussionPostings extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];
    protected $allowedIncludePaths = [
        \JsonApi\Schemas\Forum\Posting::REL_DISCUSSION,
        \JsonApi\Schemas\Forum\Posting::REL_POSTING,
        \JsonApi\Schemas\Forum\Posting::REL_OPENGRAPH_URLS,
        \JsonApi\Schemas\Forum\Posting::REL_AUTHOR,
        \JsonApi\Schemas\Forum\Posting::REL_REACTIONS,
        \JsonApi\Schemas\Forum\Posting::REL_REACTIONS_USER
    ];

    public function __invoke(Request $request, Response $response, $args)
    {
        $discussion = Discussion::find($args['discussion_id']);
        if (!$discussion) {
            throw new RecordNotFoundException();
        }

        $range = get_object_by_range_id($discussion->range_id);
        if (!$range) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);
        if (!Authority::canShowForum($user, $range)) {
            throw new AuthorizationFailedException();
        }

        $postings = $discussion->postings ?? \SimpleORMapCollection::createFromArray([]);

        PostingRead::updateUserReadPoint($user->user_id, $discussion->discussion_id, count($postings));

        return $this->getPaginatedContentResponse(
            $postings->limit(...$this->getOffsetAndLimit()),
            count($postings)
        );
    }
}
