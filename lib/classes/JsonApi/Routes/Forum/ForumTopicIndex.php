<?php
namespace JsonApi\Routes\Forum;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\RangeAuthority;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;
use Forum\ForumTopic;

class ForumTopicIndex extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];
    protected $allowedFilteringParameters = ['course-id'];
    protected $allowedIncludePaths = [
        \JsonApi\Schemas\Forum\ForumTopic::REL_CATEGORY
    ];

    public function __invoke(Request $request, Response $response, $args)
    {
        $range = get_object_by_range_id($args['range_id']);
        if (!$range) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);
        if (!RangeAuthority::canShowRange($user, $range)) {
            throw new AuthorizationFailedException();
        }

        $topics = ForumTopic::getCourseTopics($range->id);

        return $this->getPaginatedContentResponse(
            array_slice($topics, ...$this->getOffsetAndLimit()),
            count($topics)
        );
    }
}
