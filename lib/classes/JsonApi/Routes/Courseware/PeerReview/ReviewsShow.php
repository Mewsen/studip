<?php

namespace JsonApi\Routes\Courseware\PeerReview;

use Courseware\PeerReview;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\Courseware\Authority;
use JsonApi\Schemas\Courseware\PeerReview as PeerReviewSchema;
use JsonApi\Schemas\Courseware\Task as TaskSchema;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Displays one PeerReview.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class ReviewsShow extends JsonApiController
{
    protected $allowedIncludePaths = [
        PeerReviewSchema::REL_PROCESS,
        PeerReviewSchema::REL_REVIEWER,
        PeerReviewSchema::REL_SUBMITTER,
        PeerReviewSchema::REL_TASK,
        PeerReviewSchema::REL_TASK . '.' . TaskSchema::REL_STRUCTURAL_ELEMENT,
        PeerReviewSchema::REL_TASK . '.' . TaskSchema::REL_TASK_GROUP,
    ];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param array $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $resource = PeerReview::find($args['id']);
        if (!$resource) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canShowPeerReview($this->getUser($request), $resource)) {
            throw new AuthorizationFailedException();
        }

        return $this->getContentResponse($resource);
    }
}
