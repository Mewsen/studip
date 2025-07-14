<?php
namespace JsonApi\Routes\Forum;

use Forum\ForumDiscussion;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\RangeAuthority;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;

class ForumDiscussionShow extends JsonApiController
{
    protected $allowedIncludePaths = [
        \JsonApi\Schemas\Forum\ForumDiscussion::REL_POSTINGS,
        \JsonApi\Schemas\Forum\ForumCategory::REL_TOPICS,
        \JsonApi\Schemas\Forum\ForumDiscussion::REL_CATEGORY,
        \JsonApi\Schemas\Forum\ForumDiscussion::REL_DISCUSSION_TYPE
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

        return $this->getContentResponse($discussion);
    }
}
