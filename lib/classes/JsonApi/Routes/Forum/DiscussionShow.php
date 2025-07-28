<?php
namespace JsonApi\Routes\Forum;

use Forum\Discussion;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;

class DiscussionShow extends JsonApiController
{
    protected $allowedIncludePaths = [
        \JsonApi\Schemas\Forum\Discussion::REL_POSTINGS,
        \JsonApi\Schemas\Forum\Category::REL_TOPICS,
        \JsonApi\Schemas\Forum\Discussion::REL_CATEGORY,
        \JsonApi\Schemas\Forum\Discussion::REL_DISCUSSION_TYPE
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

        return $this->getContentResponse($discussion);
    }
}
