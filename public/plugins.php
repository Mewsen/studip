<?php
/*
 * Copyright (C) 2007 - Marcus Lunzenauer <mlunzena@uos.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Psr\Http\Server\RequestHandlerInterface;

require '../lib/bootstrap.php';

// prepare environment
URLHelper::setBaseUrl($GLOBALS['ABSOLUTE_URI_STUDIP']);

// Build PHP_DI Container
$container = app();

// Instantiate the app
AppFactory::setContainer($container);
$app = AppFactory::create();
$container->set(App::class, $app);
$app->setBasePath($GLOBALS['CANONICAL_RELATIVE_PATH_STUDIP'] . 'plugins.php');
$plugin_dispatch = function (ServerRequestInterface $request, RequestHandlerInterface $handler) use ($app) {
    $responseFactory = app(ResponseFactoryInterface::class);
    try {
        // get plugin class from request
        $dispatch_to = Request::pathInfo();
        list($plugin_class, $unconsumed) = PluginEngine::routeRequest($dispatch_to);

        // handle legacy forum plugin URLs
        if ($plugin_class === 'coreforum') {
            $response = $responseFactory->createResponse(302);
            return $response->withHeader('Location', URLHelper::getURL('dispatch.php/course/forum/' . $unconsumed));
        }

        // create an instance of the queried plugin
        $plugin = PluginEngine::getPlugin($plugin_class);

        // user is not permitted, show login screen
        if (is_null($plugin)) {
            // TODO (mlunzena) should not getPlugin throw this exception?
            throw new AccessDeniedException(_('Sie besitzen keine Rechte zum Aufruf dieses Plugins.'));
        }

        // set default page title
        PageLayout::setTitle($plugin->getPluginName());

        $route_callable = $plugin->getRouteCallable($unconsumed);
        $app->any(Request::pathInfo(), $route_callable);
    } catch (AccessDeniedException $ade) {
        $_SESSION['redirect_after_login'] = Request::url();
        $response = $responseFactory->createResponse(302);
        return $response->withHeader('Location', URLHelper::getURL('dispatch.php/login'));
    }
    return $handler->handle($request);
};

$app->add($plugin_dispatch);
$app->add(app(Studip\Middleware\SeminarOpenMiddleware::class));
$app->add(app(Studip\Middleware\AuthenticationMiddleware::class));
auth()->setNobody(true);
$app->add(app(Studip\Middleware\SessionMiddleware::class));

NotificationCenter::postNotification('SLIM_BEFORE_RUN', $app);
$app->run();
