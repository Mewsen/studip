<?php
namespace JsonApi\Routes\Forum;

use JsonApi\Errors\RecordNotFoundException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;

class ForumDiscussionTypeDiscussions extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];
    protected $allowedIncludePaths = [
        \JsonApi\Schemas\Forum\ForumDiscussionType::REL_DISCUSSIONS
    ];

    public function __invoke(Request $request, Response $response, $args)
    {
        $discussion_type = \Forum\ForumDiscussionType::find($args['type_id']);

        if (!$discussion_type) {
            throw new RecordNotFoundException();
        }

        $discussions = $discussion_type->discussions ?? \SimpleORMapCollection::createFromArray([]);

        return $this->getPaginatedContentResponse(
            $discussions->limit(...$this->getOffsetAndLimit()),
            count($discussions)
        );
    }
}
