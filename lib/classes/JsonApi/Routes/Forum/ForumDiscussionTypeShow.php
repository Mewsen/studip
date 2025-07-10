<?php
namespace JsonApi\Routes\Forum;

use Forum\ForumDiscussionType;
use JsonApi\Errors\RecordNotFoundException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;

class ForumDiscussionTypeShow extends JsonApiController
{
    protected $allowedIncludePaths = [
        \JsonApi\Schemas\Forum\ForumDiscussionType::REL_DISCUSSIONS
    ];

    public function __invoke(Request $request, Response $response, $args)
    {
        $discussion_type = ForumDiscussionType::find($args['type_id']);
        if (!$discussion_type) {
            throw new RecordNotFoundException();
        }

        return $this->getContentResponse($discussion_type);
    }
}
