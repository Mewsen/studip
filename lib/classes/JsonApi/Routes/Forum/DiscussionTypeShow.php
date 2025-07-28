<?php
namespace JsonApi\Routes\Forum;

use Forum\DiscussionType;
use JsonApi\Errors\RecordNotFoundException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;

class DiscussionTypeShow extends JsonApiController
{
    protected $allowedIncludePaths = [
        \JsonApi\Schemas\Forum\DiscussionType::REL_DISCUSSIONS
    ];

    public function __invoke(Request $request, Response $response, $args)
    {
        $discussion_type = DiscussionType::find($args['type_id']);
        if (!$discussion_type) {
            throw new RecordNotFoundException();
        }

        return $this->getContentResponse($discussion_type);
    }
}
