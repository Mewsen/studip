<?php

namespace JsonApi\Routes\Blubber;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\BadRequestException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Adds a new participant to a blubber private thread.
 */
class ParticipationsCreate extends JsonApiController
{
    use ValidationTrait;

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->validate($request);

        $user = $this->getUser($request);

        $thread = \BlubberThread::find($args['thread_id']);

        if (empty($thread)) {
            throw new RecordNotFoundException();
        }

        if (!$thread->isOfContextType(\BlubberThread::CTX_TYPE_PRIVATE)) {
            throw new BadRequestException('A participant can only be added to a private blubber thread.');
        }

        if (!Authority::canAddParticipantToPrivateBlubberThread($user, $thread)) {
            throw new AuthorizationFailedException();
        }

        $userIdToAdd = self::arrayGet($json, 'data.attributes.user-id', '');
        $externalContact = self::arrayGet($json, 'data.attributes.external-contact', false);

        // If the user is already participant, just return the existing participation.
        $existingParticipation = \BlubberParticipation::findUserParticipationIn($thread->id, $userIdToAdd);
        if (!empty($existingParticipation)) {
            return $this->getCreatedResponse($existingParticipation);
        }

        // Add the user as participant to the thread.
        $participation = \BlubberParticipation::create([
            'thread_id' => $thread->id,
            'user_id' => $userIdToAdd,
            'external_contact' => $externalContact
        ]);

        return $this->getCreatedResponse($participation);
    }

    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data.attributes.user-id')) {
            return 'Attribute \'user-id\' is required.';
        }
    }
}
