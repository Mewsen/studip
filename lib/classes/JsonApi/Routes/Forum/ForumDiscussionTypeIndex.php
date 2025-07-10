<?php
namespace JsonApi\Routes\Forum;

use Forum\ForumDiscussionType;
use JsonApi\Errors\BadRequestException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;

class ForumDiscussionTypeIndex extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];
    protected $allowedIncludePaths = [
        \JsonApi\Schemas\Forum\ForumDiscussionType::REL_DISCUSSIONS
    ];

    public function __invoke(Request $request, Response $response, $args)
    {
        $discussion_types = ForumDiscussionType::findBySQL('1');

        return $this->getPaginatedContentResponse(
            array_slice($discussion_types, ...$this->getOffsetAndLimit()),
            count($discussion_types)
        );
    }
}
