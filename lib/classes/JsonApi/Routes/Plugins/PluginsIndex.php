<?php

namespace JsonApi\Routes\Plugins;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PluginsIndex extends JsonApiController
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $user = $this->getUser($request);
        if (!$user || $user->perms !== 'root') {
            throw new AuthorizationFailedException();
        }

        return $this->getContentResponse(\Plugin::findBySQL('1'));
    }
}
