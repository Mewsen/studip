<?php

namespace JsonApi\Routes\Community;

use Community\CommunityGroup;
use Community\CommunityGroupParticipant;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CommunityGroupParticipantShow extends JsonApiController
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $participant = CommunityGroupParticipant::find($args['id']);
        if (!$participant) {
            throw new RecordNotFoundException();
        }

        $group = $participant->group;
        $user = $this->getUser($request);

        if (!Authority::canShowCommunityGroupParticipant($user, $group)) {
            throw new AuthorizationFailedException();
        }

        return $this->getContentResponse($participant);
    }
}