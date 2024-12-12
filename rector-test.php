<?php

include 'vendor/flexi/lib/flexi.php';
require_once 'vendor/trails/trails.php';

include_once 'app/controllers/studip_controller.php';
require 'app/controllers/plugin_controller.php';
require 'app/controllers/studip_controller_properties_trait.php';
require 'app/controllers/studip_response.php';

$foo = get_config('FOO_BAR');;
$foo = studip_json_encode($foo);
$foo = studip_json_decode($foo, true);
echo transformBeforeSave(smile($foo));

Sidebar::setImage('foo.gif');
$bar = Sidebar::getImage();
Sidebar::removeImage();

/** @var StudipCache $cache */
$cache = StudipCacheFactory::getCache();
if ($cache instanceof StudipDbCache) {
    echo 'Cached in database';
}

$factory = new Flexi_TemplateFactory(__DIR__);
/** @var Flexi_Template $template */
$template = $factory->open('foo.php');
if ($template instanceof Flexi_PhpTemplate) {
    echo 'Template is php';
}

try {
    $dispatcher = new Trails_Dispatcher('', '', '');
    $controller = new Trails_Controller($dispatcher);
    $flash = new Trails_Flash();
    $inflector = new Trails_Inflector();
} catch (Trails_DoubleRenderError $e) {
    echo 'double render';
} catch (Trails_MissingFile $e) {
    echo 'missing file';
} catch (Trails_RoutingError $e) {
    echo 'routing error';
} catch (Trails_SessionRequiredException $e) {
    echo 'session required';
} catch (Trails_UnknownAction $e) {
    echo 'unknown action';
} catch (Trails_UnknownController $e) {
    echo 'unknown controller';
} catch (Trails_Exception $e) {
    echo 'some exception';
}

class TestBlockType extends \Courseware\BlockTypes\BlockType
{
    public static function getType(): string
    {
        return '';
    }

    public static function getTitle(): string
    {
        return '';
    }

    public static function getDescription(): string
    {
        return '';
    }

    public function initialPayload(): array
    {
        return [];
    }

    public static function getCategories(): array
    {
        return [];
    }

    public static function getContentTypes(): array
    {
        return [];
    }

    public static function getFileTypes(): array
    {
        return [];
    }

    public static function getJsonSchema(): \Opis\JsonSchema\Schema
    {
        return \Opis\JsonSchema\Schema::fromJsonString(file_get_contents(__FILE__));
    }
}
