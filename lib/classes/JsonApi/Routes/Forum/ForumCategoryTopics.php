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

class ForumCategoryTopics extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];
    protected $allowedIncludePaths = [
        \JsonApi\Schemas\Forum\ForumCategory::REL_TOPICS
    ];

    public function __invoke(Request $request, Response $response, $args)
    {
        $category = \Forum\ForumCategory::find($args['category_id']);

        if (!$category) {
            throw new RecordNotFoundException();
        }

        if (!$course = \Course::find($category->range_id)) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);
        if (!CourseAuthority::canShowCourse($user, $course, CourseAuthority::SCOPE_BASIC)) {
            throw new AuthorizationFailedException();
        }

        $topics = $category->topics ?? \SimpleORMapCollection::createFromArray([]);

        return $this->getPaginatedContentResponse(
            $topics->limit(...$this->getOffsetAndLimit()),
            count($topics)
        );
    }
}
