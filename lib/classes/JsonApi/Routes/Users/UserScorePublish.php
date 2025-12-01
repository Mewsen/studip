<?php

namespace JsonApi\Routes\Users;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserScorePublish extends JsonApiController
{
    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $targetedUser = \User::find($args['id']);
        if (!$targetedUser) {
            throw new RecordNotFoundException();
        }
        $performedBy = $this->getUser($request);
        if (!Authority::canPublishScore($performedBy, $targetedUser)) {
            throw new AuthorizationFailedException();
        }

        $targetedUser->score = \Score::getMyScore($targetedUser);
        $targetedUser->store();

        return $this->getContentResponse($targetedUser);
    }
}
