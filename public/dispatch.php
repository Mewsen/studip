<?php
use Slim\App;
use Slim\Factory\AppFactory;

/*
 * index.php - <short-description>
 *
 * Copyright (C) 2006 - Marcus Lunzenauer <mlunzena@uos.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */


require '../lib/bootstrap.php';

// prepare environment
URLHelper::setBaseUrl($GLOBALS['ABSOLUTE_URI_STUDIP']);

// Build PHP_DI Container
$container = studipApp();

// Instantiate the app
AppFactory::setContainer($container);
$app = AppFactory::create();
$container->set(App::class, $app);
$app->setBasePath($GLOBALS['CANONICAL_RELATIVE_PATH_STUDIP'] . 'dispatch.php');

$studip_dispatcher = studipApp(\Trails\Dispatcher::class);
$route_callable = $studip_dispatcher->getRouteCallable(Request::pathInfo());
$app->any(Request::pathInfo(), $route_callable);

// Add legacy director so links shall not break
$app->add(Studip\Middleware\LegacyRedirectorMiddleware::class);

NotificationCenter::postNotification('SLIM_BEFORE_RUN', $app);
$app->run();
NotificationCenter::postNotification('SLIM_AFTER_RUN', $app);
