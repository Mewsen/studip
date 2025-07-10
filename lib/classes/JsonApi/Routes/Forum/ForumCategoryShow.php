<?php
namespace JsonApi\Routes\Forum;

use Course;
use Forum\ForumCategory;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\Courses\Authority as CourseAuthority;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;

class ForumCategoryShow extends JsonApiController
{
    protected $allowedIncludePaths = [
        \JsonApi\Schemas\Forum\ForumCategory::REL_TOPICS
    ];

    public function __invoke(Request $request, Response $response, $args)
    {
        $category = ForumCategory::find($args['category_id']);
        if (!$category) {
            throw new RecordNotFoundException();
        }

        $course = Course::find($category->range_id);
        if (!$course) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);
        if (!CourseAuthority::canShowCourse($user, $course, CourseAuthority::SCOPE_BASIC)) {
            throw new AuthorizationFailedException();
        }

        return $this->getContentResponse($category);
    }
}
