<?php
namespace JsonApi\Routes\Forum;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\Courses\Authority as CourseAuthority;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;
use Forum\ForumDiscussion;
use Forum\ForumPosting;

class ForumDiscussionIndex extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];
    protected $allowedFilteringParameters = ['last-visit'];
    protected $allowedIncludePaths = [
        \JsonApi\Schemas\Forum\ForumCategory::REL_TOPICS,
        \JsonApi\Schemas\Forum\ForumDiscussion::REL_CATEGORY,
        \JsonApi\Schemas\Forum\ForumDiscussion::REL_DISCUSSION_TYPE,
        \JsonApi\Schemas\Forum\ForumDiscussion::REL_MEMBERS,
        \JsonApi\Schemas\Forum\ForumDiscussion::REL_TAGS
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

        $filtering = $this->getQueryParameters()->getFilteringParameters() ?: [];
        $last_visit = $filtering['last-visit'] ?? 0;

        if ($last_visit) {
            $recent_posts = ForumPosting::getRecentPosts($course->id, $last_visit);
            $discussions = ForumDiscussion::findBySQL(
                "discussion_id IN (:discussion_ids)",
                [
                    'discussion_ids' => array_column($recent_posts, 'discussion_id')
                ]
            );
        } else {
            $discussions = ForumDiscussion::findBySQL(
                "JOIN forum_topics USING(topic_id) WHERE forum_topics.range_id = :course_id ORDER BY position ASC, mkdate DESC",
                ['course_id' => $course->id]
            );
        }

        return $this->getPaginatedContentResponse(
            array_slice($discussions, ...$this->getOffsetAndLimit()),
            count($discussions)
        );
    }
}
