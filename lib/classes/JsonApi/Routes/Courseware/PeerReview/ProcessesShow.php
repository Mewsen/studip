<?php

namespace JsonApi\Routes\Courseware\PeerReview;

use Course;
use Courseware\PeerReviewProcess;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\Courseware\Authority;
use JsonApi\Schemas\Courseware\PeerReviewProcess as ProcessSchema;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use User;

/**
 * Displays one PeerReviewProcess.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class ProcessesShow extends JsonApiController
{
    protected $allowedIncludePaths = [
        ProcessSchema::REL_COURSE,
        ProcessSchema::REL_OWNER,
        ProcessSchema::REL_TASK_GROUP,
    ];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param array $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        /** @var ?\Courseware\PeerReviewProcess $resource */
        $resource = PeerReviewProcess::find($args['id']);
        if (!$resource) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canShowPeerReviewProcess($this->getUser($request), $resource)) {
            throw new AuthorizationFailedException();
        }

        return $this->getContentResponse($resource);
    }
}
