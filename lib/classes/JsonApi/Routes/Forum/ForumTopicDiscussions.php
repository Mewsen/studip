<?php
namespace JsonApi\Routes\Forum;

use Course;
use Forum\ForumTopic;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\Courses\Authority as CourseAuthority;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;

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
        $topic = ForumTopic::find($args['topic_id']);
        if (!$topic) {
            throw new RecordNotFoundException();
        }

        $course = Course::find($topic->range_id);
        if (!$course) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);
        if (!CourseAuthority::canShowCourse($user, $course, CourseAuthority::SCOPE_BASIC)) {
            throw new AuthorizationFailedException();
        }

        $discussions = $topic->discussions;

        return $this->getPaginatedContentResponse(
            array_slice($discussions, ...$this->getOffsetAndLimit()),
            count($discussions)
        );
    }
}
