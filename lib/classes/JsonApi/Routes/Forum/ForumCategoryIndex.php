<?php
namespace JsonApi\Routes\Forum;

use Course;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\Courses\Authority as CourseAuthority;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;
use Forum\ForumCategory;

class ForumCategoryIndex extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];
    protected $allowedIncludePaths = [
        \JsonApi\Schemas\Forum\ForumCategory::REL_TOPICS
    ];

    public function __invoke(Request $request, Response $response, $args)
    {
        $course = Course::find($args['course_id']);
        if (!$course) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);
        if (!CourseAuthority::canShowCourse($user, $course, CourseAuthority::SCOPE_BASIC)) {
            throw new AuthorizationFailedException();
        }

        $categories = ForumCategory::getCourseCategories($course->id);

        return $this->getPaginatedContentResponse(
            array_slice($categories, ...$this->getOffsetAndLimit()),
            count($categories)
        );
    }
}
