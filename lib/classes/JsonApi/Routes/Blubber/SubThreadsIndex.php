<?php

namespace JsonApi\Routes\Blubber;

use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Displays all sub blubber threads of a parent thread.
 */
class SubThreadsIndex extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        if (!$parent = \BlubberThread::find($args['id'])) {
            throw new RecordNotFoundException();
        }

        $subThreads = $parent->subthreads ?? [];

        return $this->getPaginatedContentResponse($subThreads, count($subThreads));
    }
}
