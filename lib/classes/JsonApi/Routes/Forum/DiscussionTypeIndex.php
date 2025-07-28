<?php
namespace JsonApi\Routes\Forum;

use Forum\DiscussionType;
use JsonApi\Errors\BadRequestException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;

class DiscussionTypeIndex extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];
    protected $allowedIncludePaths = [
        \JsonApi\Schemas\Forum\DiscussionType::REL_DISCUSSIONS
    ];

    public function __invoke(Request $request, Response $response, $args)
    {
        $discussion_types = DiscussionType::findBySQL('1');

        return $this->getPaginatedContentResponse(
            array_slice($discussion_types, ...$this->getOffsetAndLimit()),
            count($discussion_types)
        );
    }
}
