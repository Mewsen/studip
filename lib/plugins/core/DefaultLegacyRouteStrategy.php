<?php

namespace Studip\Plugins;

use Closure;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use StudIPPlugin;

class DefaultLegacyRouteStrategy implements LegacyRouteStrategy
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getCallable(string $unconsumedPath): Closure
    {
        return function (Request $request, Response $response) use ($unconsumedPath) {
            ob_start();

            /** @var ContainerInterface $this */
            $plugin = $this->get(StudIPPlugin::class);
            $plugin->perform($unconsumedPath);
            $args = explode('/', $unconsumedPath);
            $action = $args[0] !== '' ? array_shift($args) . '_action' : 'show_action';
            $plugin->$action(...$args);

            $content = ob_get_clean();
            $response->getBody()->write($content);
            return $response;
        };
    }
}
