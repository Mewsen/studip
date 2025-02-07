<?php

namespace Studip\Middleware;

use AccessDeniedException;
use PageLayout;
use PluginEngine;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Request;
use Slim\App;
use StudIPPlugin;
use URLHelper;

final class DispatchPluginMiddleware implements MiddlewareInterface
{
    public function __construct(
        private App $app,
        private ContainerInterface $container,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    /**
     * @SuppressWarnings(StaticAccess)
     * @SuppressWarnings(SuperGlobals)
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // get plugin class from request
        $dispatchTo = Request::pathInfo();
        [$pluginClass, $unconsumed] = PluginEngine::routeRequest($dispatchTo);

        // handle legacy forum plugin URLs
        if ($pluginClass === 'coreforum') {
            return $this->responseFactory
                ->createResponse(302)
                ->withHeader(
                    'Location',
                    URLHelper::getURL('dispatch.php/course/forum/' . $unconsumed)
                );
        }

        $plugin = PluginEngine::getPlugin($pluginClass);
        $this->container->set(StudIPPlugin::class, $plugin);

        // user is not permitted, show login screen
        if (is_null($plugin)) {
            throw new AccessDeniedException(_('Sie besitzen keine Rechte zum Aufruf dieses Plugins.'));
        }

        // set default page title
        PageLayout::setTitle($plugin->getPluginName());

        $plugin->registerSlimRoutes($this->app, $unconsumed);

        return $handler->handle($request);
    }
}
