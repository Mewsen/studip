<?php
namespace JsonApi\Routes\Forum;

use Course;
use Forum\ForumPosting;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\Courses\Authority as CourseAuthority;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;
use SimpleORMapCollection;

class ForumPostingReactions extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];
    protected $allowedIncludePaths = [
        \JsonApi\Schemas\Forum\ForumPostingReaction::REL_POSTING,
        \JsonApi\Schemas\Forum\ForumPostingReaction::REL_USER
    ];

    public function __invoke(Request $request, Response $response, $args)
    {
        $posting = ForumPosting::find($args['posting_id']);
        if (!$posting) {
            throw new RecordNotFoundException();
        }

        $course = Course::find($posting->range_id);
        if (!$course) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);
        if (!CourseAuthority::canShowCourse($user, $course, CourseAuthority::SCOPE_BASIC)) {
            throw new AuthorizationFailedException();
        }

        $reactions = $posting->reactions ?? SimpleORMapCollection::createFromArray([]);

        return $this->getPaginatedContentResponse(
            $reactions->limit(...$this->getOffsetAndLimit()),
            count($reactions)
        );
    }
}
