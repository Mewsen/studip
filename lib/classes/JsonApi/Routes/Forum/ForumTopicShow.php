<?php
namespace JsonApi\Routes\Forum;

use JsonApi\Routes\Courses\Authority as CourseAuthority;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Forum\ForumTopic;

class ForumTopicShow extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];

    public function __invoke(Request $request, Response $response, $args)
    {
        if (!$topic = ForumTopic::find($args['topic_id'])) {
            throw new RecordNotFoundException();
        }

        if (!$course = \Course::find($topic->range_id)) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);
        if (!CourseAuthority::canShowCourse($user, $course, CourseAuthority::SCOPE_BASIC)) {
            throw new AuthorizationFailedException();
        }

        return $this->getContentResponse($topic);
    }
}
