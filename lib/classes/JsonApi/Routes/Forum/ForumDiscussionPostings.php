<?php
namespace JsonApi\Routes\Forum;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\Courses\Authority as CourseAuthority;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;
use Forum\ForumPosting;
use Forum\ForumPostingRead;

class ForumDiscussionPostings extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];
    protected $allowedIncludePaths = [
        \JsonApi\Schemas\Forum\ForumPosting::REL_DISCUSSION,
        \JsonApi\Schemas\Forum\ForumPosting::REL_POSTING,
        \JsonApi\Schemas\Forum\ForumPosting::REL_OPENGRAPH_URLS,
        \JsonApi\Schemas\Forum\ForumPosting::REL_AUTHOR,
        \JsonApi\Schemas\Forum\ForumPosting::REL_REACTIONS,
        \JsonApi\Schemas\Forum\ForumPosting::REL_REACTIONS_USER
    ];

    public function __invoke(Request $request, Response $response, $args)
    {
        $discussion = \Forum\ForumDiscussion::find($args['discussion_id']);

        if (!$discussion) {
            throw new RecordNotFoundException();
        }

        if (!$course = \Course::find($discussion->range_id)) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);
        if (!CourseAuthority::canShowCourse($user, $course, CourseAuthority::SCOPE_BASIC)) {
            throw new AuthorizationFailedException();
        }

        $postings = ForumPosting::findBySQL("discussion_id = :discussion_id ORDER BY mkdate ASC", ['discussion_id' => $discussion->discussion_id]);

        ForumPostingRead::updateUserReadPoint($user->user_id, $discussion->discussion_id, count($postings));

        return $this->getPaginatedContentResponse(
            array_slice($postings, ...$this->getOffsetAndLimit()),
            count($postings)
        );
    }
}
