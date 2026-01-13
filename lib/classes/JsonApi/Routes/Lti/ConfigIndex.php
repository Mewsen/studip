<?php

namespace JsonApi\Routes\Lti;

use LtiToolModule;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;

class ConfigIndex extends JsonApiController
{
    public function __invoke(Request $request, Response $response, $args): Response
    {
        $range = get_object_by_range_id($args['range_id']);
        if (!$range) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);
        if (!Authority::canShowLti($user, $range)) {
            throw new AuthorizationFailedException();
        }

        return $this->getMetaResponse([
            'is-tool-sharing-enabled' => LtiToolModule::isToolSharingEnabled(),
            'is-admin' => LtiToolModule::isAdmin($user->id),
            'is-moderator' => LtiToolModule::isModerator($range->id, $user->id)
        ]);
    }
}
