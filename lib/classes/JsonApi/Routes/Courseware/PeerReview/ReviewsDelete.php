<?php

namespace JsonApi\Routes\Courseware\PeerReview;

use Courseware\PeerReview;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\Courseware\Authority;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Delete one PeerPreview.
 */
class ReviewsDelete extends JsonApiController
{
    /**
     * @param array $args
     * @return Response
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        /** @var ?PeerReview $resource */
        $resource = PeerReview::find($args['id']);
        if (!$resource) {
            throw new RecordNotFoundException();
        }
        if (!Authority::canDeletePeerReview($this->getUser($request), $resource)) {
            throw new AuthorizationFailedException();
        }
        $resource->delete();

        return $this->getCodeResponse(204);
    }
}
