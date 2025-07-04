<?php
namespace JsonApi\Routes\Forum;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\Courses\Authority as CourseAuthority;
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
        if (!$course = \Course::find($args['course_id'])) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);
        if (!CourseAuthority::canShowCourse($user, $course, CourseAuthority::SCOPE_BASIC)) {
            throw new AuthorizationFailedException();
        }

        $topics = ForumTopic::getCourseTopics($course->id);

        return $this->getPaginatedContentResponse(
            array_slice($topics, ...$this->getOffsetAndLimit()),
            count($topics)
        );
    }
}
