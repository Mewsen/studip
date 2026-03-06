<?php

use Slim\App;
use Slim\Factory\AppFactory;
use Studip\Middleware\AuthenticationMiddleware;
use Studip\Middleware\DispatchPluginMiddleware;
use Studip\Middleware\HandleAccessDeniedMiddleware;
use Studip\Middleware\SeminarOpenMiddleware;
use Studip\Middleware\SessionMiddleware;
use Studip\Middleware\TrailingSlash;

require '../lib/bootstrap.php';

// prepare environment
URLHelper::setBaseUrl($GLOBALS['ABSOLUTE_URI_STUDIP']);

// Build PHP_DI Container
$container = studipApp();

// Instantiate the app
AppFactory::setContainer($container);
$app = AppFactory::create();
$container->set(App::class, $app);
$app->setBasePath($GLOBALS['CANONICAL_RELATIVE_PATH_STUDIP'] . 'plugins.php');

$app->add(DispatchPluginMiddleware::class);
$app->add(HandleAccessDeniedMiddleware::class);
$app->add(SeminarOpenMiddleware::class);
$app->add(AuthenticationMiddleware::class);
$app->add(SessionMiddleware::class);
$app->add(TrailingSlash::class);

auth()->setNobody(true);

NotificationCenter::postNotification('SLIM_BEFORE_RUN', $app);
$app->run();
NotificationCenter::postNotification('SLIM_AFTER_RUN', $app);
