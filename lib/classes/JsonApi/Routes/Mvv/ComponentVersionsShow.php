<?php

namespace JsonApi\Routes\Mvv;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;

class ComponentVersionsShow extends JsonApiController
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $version = \StgteilVersion::find($args['id']);
        if (!$version) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canShowComponentVersion($this->getUser($request), $version)) {
            throw new AuthorizationFailedException();
        }

        return $this->getContentResponse($version);
    }
}
