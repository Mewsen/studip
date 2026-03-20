<?php

namespace JsonApi\Routes\Lti;

use Lti\Registration;
use JsonApi\JsonApiController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Schemas\Lti\Registration as RegistrationSchema;

class RegistrationShow extends JsonApiController
{
    protected $allowedIncludePaths = [
        RegistrationSchema::REL_RANGE,
        RegistrationSchema::REL_DEPLOYMENTS
    ];

    public function __invoke(Request $request, Response $response, $args): Response
    {
        if (!$registration = Registration::find($args['id'])) {
            throw new RecordNotFoundException();
        }

        if ($registration->range_id !== 'global' && !Authority::canShowRegistration($registration->range, $this->getUser($request))) {
            throw new AuthorizationFailedException();
        }

        return $this->getContentResponse($registration);
    }
}
