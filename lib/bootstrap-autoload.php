<?php
// Setup autoloading
require 'lib/classes/StudipAutoloader.php';
StudipAutoloader::register();

class_alias(\Studip\Cache\Factory::class, 'StudipCacheFactory');
class_alias(\Studip\Cache\Cache::class, 'StudipCache');
class_alias(Flexi\PhpTemplate::class, 'Flexi_PhpTemplate');
class_alias(Flexi\Template::class, 'Flexi_Template');
class_alias(Flexi\Factory::class, 'Flexi_TemplateFactory');
class_alias(Flexi\TemplateNotFoundException::class, 'Flexi_TemplateNotFoundException');
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
