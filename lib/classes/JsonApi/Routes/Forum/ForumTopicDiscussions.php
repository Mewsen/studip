<?php
namespace JsonApi\Routes\Forum;

use JsonApi\Errors\BadRequestException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\Courses\Authority as CourseAuthority;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;
use Forum\ForumCategory;
use Forum\ForumSubscription;
use Forum\ForumTopic;

class ForumTopicDiscussions extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];
    protected $allowedIncludePaths = [
        \JsonApi\Schemas\Forum\ForumCategory::REL_TOPICS,
        \JsonApi\Schemas\Forum\ForumDiscussion::REL_CATEGORY,
        \JsonApi\Schemas\Forum\ForumDiscussion::REL_DISCUSSION_TYPE,
        \JsonApi\Schemas\Forum\ForumDiscussion::REL_MEMBERS,
        \JsonApi\Schemas\Forum\ForumDiscussion::REL_TAGS
    ];

    public function __invoke(Request $request, Response $response, $args)
    {
        $topic = \Forum\ForumTopic::find($args['topic_id']);

        if (!$topic) {
            throw new RecordNotFoundException();
        }

        if (!$course = \Course::find($topic->range_id)) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);
        if (!CourseAuthority::canShowCourse($user, $course, CourseAuthority::SCOPE_BASIC)) {
            throw new AuthorizationFailedException();
        }

        $discussions = $topic->discussions ?? \SimpleORMapCollection::createFromArray([]);

        return $this->getPaginatedContentResponse(
            $discussions->limit(...$this->getOffsetAndLimit()),
            count($discussions)
        );
    }
}
