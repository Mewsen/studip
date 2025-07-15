<?php
namespace JsonApi\Routes\Forum;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\RangeAuthority;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;
use Forum\ForumDiscussion;

class ForumDiscussionIndex extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];
    protected $allowedFilteringParameters = ['last-visit'];
    protected $allowedIncludePaths = [
        \JsonApi\Schemas\Forum\ForumCategory::REL_TOPICS,
        \JsonApi\Schemas\Forum\ForumDiscussion::REL_CATEGORY,
        \JsonApi\Schemas\Forum\ForumDiscussion::REL_USER,
        \JsonApi\Schemas\Forum\ForumDiscussion::REL_DISCUSSION_TYPE,
        \JsonApi\Schemas\Forum\ForumDiscussion::REL_MEMBERS,
        \JsonApi\Schemas\Forum\ForumDiscussion::REL_TAGS
    ];


    public function __invoke(Request $request, Response $response, $args)
    {
        $range = get_object_by_range_id($args['range_id']);
        if (!$range) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);
        if (!RangeAuthority::canShowRange($user, $range)) {
            throw new AuthorizationFailedException();
        }

        $filtering = $this->getQueryParameters()->getFilteringParameters() ?: [];
        $last_visit = $filtering['last-visit'] ?? 0;

        $discussions = ForumDiscussion::getCourseDiscussions($range->id, $last_visit);

        return $this->getPaginatedContentResponse(
            array_slice($discussions, ...$this->getOffsetAndLimit()),
            count($discussions)
        );
    }
}
