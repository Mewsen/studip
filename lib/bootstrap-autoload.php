<?php
// Setup autoloading
require 'lib/classes/StudipAutoloader.php';
StudipAutoloader::register();

// General classes folders
StudipAutoloader::addAutoloadPath('lib/models');
StudipAutoloader::addAutoloadPath('lib/models/calendar');
StudipAutoloader::addAutoloadPath('lib/models/resources');
StudipAutoloader::addAutoloadPath('lib/classes');
StudipAutoloader::addAutoloadPath('lib/classes', 'Studip');

// Plugins
StudipAutoloader::addAutoloadPath('lib/plugins/core');
StudipAutoloader::addAutoloadPath('lib/plugins/db');
StudipAutoloader::addAutoloadPath('lib/plugins/engine');

// Specialized folders
StudipAutoloader::addAutoloadPath('lib/classes/admission');
StudipAutoloader::addAutoloadPath('lib/classes/admission/userfilter');
StudipAutoloader::addAutoloadPath('lib/classes/auth_plugins');
StudipAutoloader::addAutoloadPath('lib/classes/calendar');

StudipAutoloader::addAutoloadPath('lib/classes/cache', 'Studip\\Cache');
class_alias(\Studip\Cache\Factory::class, 'StudipCacheFactory');
class_alias(\Studip\Cache\Cache::class, 'StudipCache');

StudipAutoloader::addAutoloadPath('lib/classes/exportdocument');
StudipAutoloader::addAutoloadPath('lib/classes/forms');
StudipAutoloader::addAutoloadPath('lib/classes/globalsearch');
StudipAutoloader::addAutoloadPath('lib/classes/helpbar');
StudipAutoloader::addAutoloadPath('lib/classes/librarysearch/resultparsers');
StudipAutoloader::addAutoloadPath('lib/classes/librarysearch/searchmodules');
StudipAutoloader::addAutoloadPath('lib/classes/librarysearch');
StudipAutoloader::addAutoloadPath('lib/classes/searchtypes');
StudipAutoloader::addAutoloadPath('lib/classes/sidebar');
StudipAutoloader::addAutoloadPath('lib/classes/visibility');
StudipAutoloader::addAutoloadPath('lib/classes/coursewizardsteps');
StudipAutoloader::addAutoloadPath('lib/classes/wiki');

StudipAutoloader::addAutoloadPath('lib/calendar');
StudipAutoloader::addAutoloadPath('lib/calendar', 'Studip\\Calendar');
StudipAutoloader::addAutoloadPath('lib/exceptions');
StudipAutoloader::addAutoloadPath('lib/exceptions/resources');
StudipAutoloader::addAutoloadPath('lib/filesystem');
StudipAutoloader::addAutoloadPath('lib/migrations');
StudipAutoloader::addAutoloadPath('lib/modules');
StudipAutoloader::addAutoloadPath('lib/navigation');
StudipAutoloader::addAutoloadPath('lib/phplib');
StudipAutoloader::addAutoloadPath('lib/raumzeit');
StudipAutoloader::addAutoloadPath('lib/resources');
StudipAutoloader::addAutoloadPath('lib/activities', 'Studip\\Activity');

StudipAutoloader::addAutoloadPath('lib/calendar/lib');
StudipAutoloader::addAutoloadPath('lib/elearning');
StudipAutoloader::addAutoloadPath('lib/extern');
StudipAutoloader::addAutoloadPath('lib/ilias_interface');

// Flexi
StudipAutoloader::addAutoloadPath('lib/flexi', 'Flexi');
class_alias(Flexi\PhpTemplate::class, 'Flexi_PhpTemplate');
class_alias(Flexi\Template::class, 'Flexi_Template');
class_alias(Flexi\Factory::class, 'Flexi_TemplateFactory');
class_alias(Flexi\TemplateNotFoundException::class, 'Flexi_TemplateNotFoundException');

// Trails
StudipAutoloader::addAutoloadPath('lib/trails', 'Trails');
class_alias(Trails\Controller::class, 'Trails_Controller');
class_alias(Trails\Dispatcher::class, 'Trails_Dispatcher');
class_alias(Trails\Exception::class, 'Trails_Exception');
class_alias(Trails\Flash::class, 'Trails_Flash');
class_alias(Trails\Inflector::class, 'Trails_Inflector');
class_alias(Trails\Response::class, 'Trails_Response');

class_alias(Trails\Exceptions\DoubleRenderError::class, 'Trails_DoubleRenderError');
class_alias(Trails\Exceptions\MissingFile::class, 'Trails_MissingFile');
class_alias(Trails\Exceptions\RoutingError::class, 'Trails_RoutingError');
class_alias(Trails\Exceptions\SessionRequiredException::class, 'Trails_SessionRequiredException');
class_alias(Trails\Exceptions\UnknownAction::class, 'Trails_UnknownAction');
class_alias(Trails\Exceptions\UnknownController::class, 'Trails_UnknownController');

// Messy file names
StudipAutoloader::addClassLookups([
    'email_validation_class' => 'lib/phplib/email_validation.class.php',
    'messaging'              => 'lib/messaging.inc.php',
    'StudipPlugin'           => 'lib/plugins/core/StudIPPlugin.class.php',
    'MVVController'          => 'app/controllers/module/mvv_controller.php'
]);

// Vendor
StudipAutoloader::addClassLookups([
    'PasswordHash' => 'vendor/phpass/PasswordHash.php',
]);

// XMLRpc
StudipAutoloader::addClassLookup(
    ['xmlrpcval', 'xmlrpcmsg', 'xmlrpcresp', 'xmlrpc_client'],
    'composer/phpxmlrpc/phpxmlrpc/lib/xmlrpc.inc'
);
StudipAutoloader::addClassLookup(
    ['xmlrpc_server'],
    'composer/phpxmlrpc/phpxmlrpc/lib/xmlrpcs.inc'
);
