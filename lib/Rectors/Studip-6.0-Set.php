<?php
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\FuncCall\RenameFunctionRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Studip\Rectors\Studip60\RemoveFunctionCallRector;
use Studip\Rectors\Studip60\RemoveIncludesRector;

return RectorConfig::configure()
    ->withRules([
        Studip\Rectors\Studip60\RemoveGetConfigRector::class,
        Studip\Rectors\Studip60\RemoveSidebarMethodsRector::class,
        Studip\Rectors\Studip60\RewriteCoursewareBlockTypesRector::class,
        Studip\Rectors\Studip60\ReplacePageCloseRector::class,
    ])
    ->withConfiguredRule(RenameFunctionRector::class, [
        'studip_json_decode' => 'json_decode',
        'studip_json_encode' => 'json_encode',
    ])
    ->withConfiguredRule(RemoveIncludesRector::class, [
        'vendor/flexi',
        'vendor/trails',
        'app/controllers/authenticated_controller.php',
        'app/controllers/plugin_controller.php',
        'app/controllers/studip_controller.php',
        'app/controllers/studip_controller_properties_trait.php',
        'app/controllers/studip_response.php',
    ])
    ->withConfiguredRule(RemoveFunctionCallRector::class, [
        'smile',
        'transformBeforeSave',
    ])
    ->withConfiguredRule(RenameClassRector::class, [
        'Flexi_PhpTemplate' => 'Flexi\PhpTemplate',
        'Flexi_Template' => 'Flexi\Template',
        'Flexi_TemplateFactory' => 'Flexi\Factory',

        'StudipCacheFactory' => 'Studip\Cache\Factory',
        'StudipCache' => 'Studip\Cache\Cache',
        'StudipDbCache' => 'Studip\Cache\DbCache',

        'Trails_Controller' => 'Trails\Controller',
        'Trails_Dispatcher' => 'Trails\Dispatcher',
        'Trails_Exception' => 'Trails\Exception',
        'Trails_Flash' => 'Trails\Flash',
        'Trails_Inflector' => 'Trails\Inflector',
        'Trails_Response' => 'Trails\Response',
        'Trails_DoubleRenderError' => 'Trails\Exceptions\DoubleRenderError',
        'Trails_MissingFile' => 'Trails\Exceptions\MissingFile',
        'Trails_RoutingError' => 'Trails\Exceptions\RoutingError',
        'Trails_SessionRequired' => 'Trails\Exceptions\SessionRequiredException',
        'Trails_UnknownAction' => 'Trails\Exceptions\UnknownAction',
        'Trails_UnknownController' => 'Trails\Exceptions\UnknownController',
    ]);
