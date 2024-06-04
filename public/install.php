<?php
/**
 * @author  Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @license GPL2 or any later version
 */
session_start();

$GLOBALS['STUDIP_BASE_PATH'] = realpath(__DIR__ . '/..');

if (file_exists($GLOBALS['STUDIP_BASE_PATH'] . '/config/config_local.inc.php')
    && !isset($_SESSION['STUDIP_INSTALLATION'])
) {
    throw new Exception(_('Diese Installation ist bereits konfiguriert'));
}

set_include_path($GLOBALS['STUDIP_BASE_PATH']);

require 'composer/autoload.php';
require 'lib/visual.inc.php';
require 'lib/functions.php';
require 'lib/classes/URLHelper.php';
require 'lib/classes/LayoutMessage.interface.php';
require 'lib/classes/MessageBox.class.php';
require 'lib/classes/Request.class.php';
require 'lib/classes/Interactable.class.php';
require 'lib/classes/Button.class.php';
require 'lib/classes/LinkButton.class.php';
require 'lib/classes/StudipInstaller.php';
require 'lib/classes/SystemChecker.php';
require 'lib/classes/Markup.class.php';
require 'lib/exceptions/AccessDeniedException.php';
require 'lib/flexi/Factory.php';
require 'lib/flexi/Template.php';
require 'lib/flexi/PhpTemplate.php';
require 'lib/flexi/TemplateNotFoundException.php';
require 'lib/trails/Controller.php';
require 'lib/trails/Dispatcher.php';
require 'lib/trails/Exception.php';
require 'lib/trails/Flash.php';
require 'lib/trails/Inflector.php';
require 'lib/trails/Response.php';
require 'lib/trails/Exceptions/DoubleRenderError.php';
require 'lib/trails/Exceptions/MissingFile.php';
require 'lib/trails/Exceptions/RoutingError.php';
require 'lib/trails/Exceptions/SessionRequiredException.php';
require 'lib/trails/Exceptions/UnknownAction.php';
require 'lib/trails/Exceptions/UnknownController.php';
require 'vendor/phpass/PasswordHash.php';

// Mock gettext functions if extension is not available
if (!function_exists('_')) {
    function _($what) {
        return $what;
    }
} else {
    require_once 'lib/language.inc.php';

    foreach (explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '') as $lang) {
        [$lang, ] = explode(';', $lang);
        $lang = substr($lang, 0, 2);

        if (!in_array($lang, ['de', 'en'])) {
            continue;
        }

        setLocaleEnv($lang, 'studip');
        break;
    }
}

$GLOBALS['template_factory'] = new Flexi\Factory('../templates/');

# get plugin class from request
$dispatch_to = ltrim(Request::pathInfo(), '/');

$dispatcher = new Trails\Dispatcher( '../app', $_SERVER['SCRIPT_NAME'], 'admin/install');
$dispatcher->dispatch("admin/install/{$dispatch_to}");
