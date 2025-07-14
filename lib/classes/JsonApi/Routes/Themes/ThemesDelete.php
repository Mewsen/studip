<?php

namespace JsonApi\Routes\Themes;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class ThemesDelete extends JsonApiController
{
    public function __invoke(Request $request, Response $response, $args): Response
    {
        $resource = \Theme::find($args['id']);
        if (!$resource) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canDeleteTheme($this->getUser($request))) {
            throw new AuthorizationFailedException();
        }
        $resource->delete();

        return $this->getCodeResponse(204);
    }
}
