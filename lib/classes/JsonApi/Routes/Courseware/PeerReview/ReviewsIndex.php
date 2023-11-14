<?php

namespace JsonApi\Routes\Courseware\PeerReview;

use Course;
use Courseware\PeerReview;
use Courseware\PeerReviewProcess;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\BadRequestException;
use JsonApi\JsonApiController;
use JsonApi\Routes\Courses\Authority as CoursesAuthority;
use JsonApi\Routes\Courseware\Authority;
use JsonApi\Schemas\Courseware\PeerReview as PeerReviewSchema;
use JsonApi\Schemas\Courseware\Task as TaskSchema;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use User;

/**
 * Displays all PeerReviews of a course.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class ReviewsIndex extends JsonApiController
{
    protected $allowedIncludePaths = [
        PeerReviewSchema::REL_PROCESS,
        PeerReviewSchema::REL_REVIEWER,
        PeerReviewSchema::REL_SUBMITTER,
        PeerReviewSchema::REL_TASK,
        PeerReviewSchema::REL_TASK . '.' . TaskSchema::REL_STRUCTURAL_ELEMENT,
        PeerReviewSchema::REL_TASK . '.' . TaskSchema::REL_TASK_GROUP,
    ];

    protected $allowedPagingParameters = ['offset', 'limit'];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param array $args
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        /** @var ?Course $course */
        $course = Course::find($args['id']);
        if (!$course) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);
        $this->authorize($user);

        $resources = $this->findPeerReviews($course, $user);

        return $this->getPaginatedContentResponse(
            array_slice($resources, ...$this->getOffsetAndLimit()),
            count($resources)
        );
    }

    /**
     * @throws AuthorizationFailedException
     */
    private function authorize(User $user): void
    {
        if (!Authority::canIndexPeerReviews($user)) {
            throw new AuthorizationFailedException();
        }
    }

    private function findPeerReviews(Course $course, User $user): iterable
    {
        return array_filter(PeerReview::findByCourse($course), function ($peerReview) use ($user) {
            return Authority::canShowPeerReview($user, $peerReview);
        });
    }
}
