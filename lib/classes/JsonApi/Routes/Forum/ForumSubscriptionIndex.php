<?php
namespace JsonApi\Routes\Forum;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\Courses\Authority as CourseAuthority;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;
use Forum\ForumSubscription;

class ForumSubscriptionIndex extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];
    protected $allowedIncludePaths = [
        \JsonApi\Schemas\Forum\ForumSubscription::REL_RANGE,
        \JsonApi\Schemas\Forum\ForumSubscription::REL_SUBJECT,
        \JsonApi\Schemas\Forum\ForumSubscription::REL_USER
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

        $subscriptions = ForumSubscription::findBySQL(
            "range_id = :course_id AND user_id = :user_id ORDER BY mkdate DESC",
            [
                'course_id' => $course->id,
                'user_id' => $user->user_id
            ]
        );

        return $this->getPaginatedContentResponse(
            array_slice($subscriptions, ...$this->getOffsetAndLimit()),
            count($subscriptions)
        );
    }
}
