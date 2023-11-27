<?php

// Here you can initialize variables that will be available to your tests

global $STUDIP_BASE_PATH, $ABSOLUTE_URI_STUDIP, $CACHING_ENABLE, $CACHING_FILECACHE_PATH, $SYMBOL_SHORT, $TMP_PATH, $UPLOAD_PATH, $DYNAMIC_CONTENT_PATH, $DYNAMIC_CONTENT_URL;

// common set-up, usually done by lib/bootstraph.php and
// config/config_local.inc.php when run on web server
if (!isset($STUDIP_BASE_PATH)) {
    $STUDIP_BASE_PATH = dirname(dirname(__DIR__));
    $ABSOLUTE_PATH_STUDIP = $STUDIP_BASE_PATH.'/public/';
    $UPLOAD_PATH = $STUDIP_BASE_PATH.'/data/upload_doc';
    $TMP_PATH = $TMP_PATH ?: '/tmp';
    $DYNAMIC_CONTENT_PATH = '';
    $DYNAMIC_CONTENT_URL = '';
}

// set include path
$inc_path = ini_get('include_path');
$inc_path .= PATH_SEPARATOR.$STUDIP_BASE_PATH;
$inc_path .= PATH_SEPARATOR.$STUDIP_BASE_PATH.'/config';
ini_set('include_path', $inc_path);

// config
$CACHING_ENABLE = false;
//$CACHING_FILECACHE_PATH = '/tmp';

date_default_timezone_set('Europe/Berlin');

require 'config.inc.php';

require 'lib/functions.php';
require 'lib/language.inc.php';
require 'lib/visual.inc.php';
require 'lib/calendar_functions.inc.php';
require 'lib/dates.inc.php';

$GLOBALS['_fullname_sql'] = [];
$GLOBALS['_fullname_sql']['full'] = "TRIM(CONCAT(title_front,' ',Vorname,' ',Nachname,IF(title_rear!='',CONCAT(', ',title_rear),'')))";
$GLOBALS['_fullname_sql']['full_rev'] = "TRIM(CONCAT(Nachname,', ',Vorname,IF(title_front!='',CONCAT(', ',title_front),''),IF(title_rear!='',CONCAT(', ',title_rear),'')))";
$GLOBALS['_fullname_sql']['no_title'] = "CONCAT(Vorname ,' ', Nachname)";
$GLOBALS['_fullname_sql']['no_title_rev'] = "CONCAT(Nachname ,', ', Vorname)";
$GLOBALS['_fullname_sql']['no_title_short'] = "CONCAT(Nachname,', ',UCASE(LEFT(TRIM(Vorname),1)),'.')";
$GLOBALS['_fullname_sql']['no_title_motto'] = "CONCAT(Vorname ,' ', Nachname,IF(motto!='',CONCAT(', ',motto),''))";
$GLOBALS['_fullname_sql']['full_rev_username'] = "TRIM(CONCAT(Nachname,', ',Vorname,IF(title_front!='',CONCAT(', ',title_front),''),IF(title_rear!='',CONCAT(', ',title_rear),''),' (',username,')'))";

SimpleORMap::expireTableScheme();

/**
 * @deprecated
 */
class DB_Seminar extends DB_Sql
{
    public function __construct($query = false)
    {
        parent::__construct($query);
    }
}

require_once __DIR__.'/../../composer/autoload.php';

session_id("test-session");
