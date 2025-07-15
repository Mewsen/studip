<?php
namespace JsonApi\Routes\Forum;

use Forum\ForumDiscussionType;
use JsonApi\Errors\RecordNotFoundException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;

class ForumDiscussionTypeDiscussions extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];
    protected $allowedIncludePaths = [
        \JsonApi\Schemas\Forum\ForumDiscussionType::REL_DISCUSSIONS,
        \JsonApi\Schemas\Forum\ForumDiscussion::REL_USER,
    ];

    public function __invoke(Request $request, Response $response, $args)
    {
        $discussionType = ForumDiscussionType::find($args['type_id']);
        if (!$discussionType) {
            throw new RecordNotFoundException();
        }

        $discussions = $discussionType->discussions;

        return $this->getPaginatedContentResponse(
            array_slice($discussions, ...$this->getOffsetAndLimit()),
            count($discussions)
        );
    }
}
