<?php

namespace JsonApi\Routes\Blubber;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\BadRequestException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;

/**
 * Deletes a PRIVATE blubber thread.
 */
class ThreadsDelete extends JsonApiController
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);
        if (!($thread = \BlubberThread::find($args['id']))) {
            throw new RecordNotFoundException();
        }

        if (!$thread->isOfContextType(\BlubberThread::CTX_TYPE_PRIVATE)) {
            throw new BadRequestException('Only private blubber threads can be deleted via this endpoint.');
        }

        if (!Authority::canDeleteThread($user, $thread)) {
            throw new AuthorizationFailedException();
        }

        $thread->delete();

        return $this->getCodeResponse(204);
    }
}
