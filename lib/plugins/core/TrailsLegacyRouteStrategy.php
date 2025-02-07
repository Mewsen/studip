<?php

namespace Studip\Plugins;

use Closure;
use Exception;
use PluginDispatcher;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use StudIPPlugin;
use Trails\Exceptions\UnknownAction;

class TrailsLegacyRouteStrategy implements LegacyRouteStrategy
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getCallable(string $unconsumedPath): Closure
    {
        return function (Request $request, Response $response, array $args) use ($unconsumedPath) {
            try {
                /** @var ContainerInterface $this */
                $plugin = $this->get(StudIPPlugin::class);
                $plugin->perform($unconsumedPath);

                $dispatcher = $this->get(PluginDispatcher::class);
                $handler = $dispatcher->getRouteCallable($unconsumedPath);

                return $handler($request, $response, $args);
            } catch (UnknownAction $exception) {
                $args = explode('/', $unconsumedPath);
                if ($args[0] !== '') {
                    $args = array_slice($args, 1);
                }
                throw count($args) ? $exception : new Exception(_('unbekannte Plugin-Aktion: ') . $unconsumedPath);
            }
        };
    }
}
