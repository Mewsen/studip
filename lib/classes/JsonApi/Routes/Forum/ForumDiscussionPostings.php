<?php
namespace JsonApi\Routes\Forum;

use Forum\ForumDiscussion;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\RangeAuthority;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;
use Forum\ForumPostingRead;

class ForumDiscussionPostings extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];
    protected $allowedIncludePaths = [
        \JsonApi\Schemas\Forum\ForumPosting::REL_DISCUSSION,
        \JsonApi\Schemas\Forum\ForumPosting::REL_POSTING,
        \JsonApi\Schemas\Forum\ForumPosting::REL_OPENGRAPH_URLS,
        \JsonApi\Schemas\Forum\ForumPosting::REL_AUTHOR,
        \JsonApi\Schemas\Forum\ForumPosting::REL_REACTIONS,
        \JsonApi\Schemas\Forum\ForumPosting::REL_REACTIONS_USER
    ];

    public function __invoke(Request $request, Response $response, $args)
    {
        $discussion = ForumDiscussion::find($args['discussion_id']);
        if (!$discussion) {
            throw new RecordNotFoundException();
        }

        $range = get_object_by_range_id($discussion->range_id);
        if (!$range) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);
        if (!RangeAuthority::canShowRange($user, $range)) {
            throw new AuthorizationFailedException();
        }

        $postings = $discussion->postings ?? \SimpleORMapCollection::createFromArray([]);

        ForumPostingRead::updateUserReadPoint($user->user_id, $discussion->discussion_id, count($postings));

        return $this->getPaginatedContentResponse(
            $postings->limit(...$this->getOffsetAndLimit()),
            count($postings)
        );
    }
}
