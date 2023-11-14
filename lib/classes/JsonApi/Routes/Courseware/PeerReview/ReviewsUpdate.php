<?php

namespace JsonApi\Routes\Courseware\PeerReview;

use Courseware\PeerReview;
use Courseware\TaskGroup;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\Courseware\Authority;
use JsonApi\Routes\TimestampTrait;
use JsonApi\Routes\ValidationTrait;
use JsonApi\Schemas\Courseware\PeerReview as PeerReviewSchema;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use User;

/**
 * Updates one PeerReview.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class ReviewsUpdate extends JsonApiController
{
    use TimestampTrait;
    use ValidationTrait;

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
        $json = $this->validate($request, $resource);
        $user = $this->getUser($request);
        if (!Authority::canUpdatePeerReview($user, $resource)) {
            throw new AuthorizationFailedException();
        }

        $review = $this->update($resource, $json);

        return $this->getContentResponse($review);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     *
     * @param array $json
     * @param mixed $data
     *
     * @return string|void
     */
    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data')) {
            return 'Missing `data` member at document´s top level.';
        }
        if (PeerReviewSchema::TYPE !== self::arrayGet($json, 'data.type')) {
            return 'Invalid `type` of document´s `data`.';
        }

        if (!self::arrayHas($json, 'data.attributes.assessment')) {
            return 'Missing `assessment` attribute.';
        }

        // TODO: validate assessment
    }

    private function update(PeerReview $review, array $json): PeerReview
    {
        $review->assessment = self::arrayGet($json, 'data.attributes.assessment');
        $review->store();

        return $review;
    }
}
