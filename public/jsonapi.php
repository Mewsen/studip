<?php

use Slim\Factory\AppFactory;

require __DIR__ . '/../lib/bootstrap.php';

\StudipAutoloader::addAutoloadPath($GLOBALS['STUDIP_BASE_PATH'] . DIRECTORY_SEPARATOR . 'vendor/oauth-php/library/');

// Set base url for URLHelper class
URLHelper::setBaseUrl($GLOBALS['CANONICAL_RELATIVE_PATH_STUDIP']);

// Instantiate the app
$container = app();
AppFactory::setContainer($container);
$app = AppFactory::create();
$container->set(\Slim\App::class, $app);

// Set the base path
$app->setBasePath($GLOBALS['CANONICAL_RELATIVE_PATH_STUDIP'] . 'jsonapi.php');

// Register middleware
$middleware = require 'lib/classes/JsonApi/middleware.php';
$middleware($app);

// Register routes via middleware
$app->add(Studip\Middleware\JsonApiRouteRegistrarMiddleware::class);

//register stud.ip session/auth middleware
$app->add(Studip\Middleware\AuthenticationMiddleware::class);
auth()->setNobody(true);
$app->add(Studip\Middleware\SessionMiddleware::class);

// Add Error Middleware
$displayErrors = false;
if (defined('\\Studip\\ENV')) {
    $displayErrors = constant('\\Studip\\ENV') === 'development';
}
$logError = true;
$logErrorDetails = true;

$errorMiddleware = $app->addErrorMiddleware($displayErrors, $logError, $logErrorDetails);
$errorMiddleware->setDefaultErrorHandler(new \JsonApi\Errors\ErrorHandler($app));

// Run app
$app->run();
