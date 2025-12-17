<?php

namespace JsonApi\Routes\Plugins;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PluginShow extends JsonApiController
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $user = $this->getUser($request);
        if (!$user || $user->perms !== 'root') {
            throw new AuthorizationFailedException();
        }

        $plugin = \Plugin::find($args['id']);
        if (!$plugin) {
            throw new RecordNotFoundException();
        }

        return $this->getContentResponse($plugin);
    }
}
