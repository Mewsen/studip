<?php

namespace JsonApi\Routes\Blubber;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\BadRequestException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;

/**
 * Remove a user from a PRIVATE blubber.
 */
class ParticipationsDelete extends JsonApiController
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);

        if (!($participation = \BlubberParticipation::find($args['id']))) {
            throw new RecordNotFoundException();
        }

        $thread = $participation->thread;

        // This never happens, but to be sure:
        if (!$thread->isOfContextType(\BlubberThread::CTX_TYPE_PRIVATE)) {
            throw new BadRequestException(
                'The related thread is not private, therefore the participant cannot be removed via this endpoint.'
            );
        }

        if (!Authority::canRemoveParticipantsFromThread($user, $participation)) {
            throw new AuthorizationFailedException();
        }

        // Record the user id before deleting the participation.
        $targetedUserId = $participation->user->id;
        // Remove the user from the thread.
        $participation->delete();

        // Also delete all comments of that user in that thread.
        \BlubberComment::deleteUserCommentsIn($thread->getId(), $targetedUserId);

        return $this->getCodeResponse(204);
    }
}
