<?php

namespace JsonApi\Routes\Blubber;

use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Displays all participants of a blubber private thread.
 */
class ParticipationsIndex extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        if (!$thread = \BlubberThread::find($args['thread_id'])) {
            throw new RecordNotFoundException();
        }

        $participations = $thread->participations ?? [];

        return $this->getPaginatedContentResponse($participations, count($participations));
    }
}
