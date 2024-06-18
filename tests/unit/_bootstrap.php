<?php

// Copyright (c)  2007 - Marcus Lunzenauer <mlunzena@uos.de>
//
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
//
// The above copyright notice and this permission notice shall be included in all
// copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
// SOFTWARE.

// set error reporting
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

// set include path
$inc_path = ini_get('include_path');
$inc_path .= PATH_SEPARATOR . __DIR__ . '/../..';
$inc_path .= PATH_SEPARATOR . __DIR__ . '/../../config';
ini_set('include_path', $inc_path);

global $ABSOLUTE_URI_STUDIP,
    $ASSETS_URL,
    $CACHING_ENABLE,
    $CACHING_FILECACHE_PATH,
    $CANONICAL_RELATIVE_PATH_STUDIP,
    $DYNAMIC_CONTENT_PATH,
    $DYNAMIC_CONTENT_URL,
    $STUDIP_BASE_PATH,
    $SYMBOL_SHORT,
    $TMP_PATH,
    $UPLOAD_PATH;

// load varstream for easier filesystem testing
require_once 'varstream.php';

define('TEST_FIXTURES_PATH', dirname(__DIR__) . '/_data/');

require __DIR__ . '/../../composer/autoload.php';

global $STUDIP_BASE_PATH;
$STUDIP_BASE_PATH = realpath(dirname(__DIR__) . '/..');

require 'lib/helpers.php';
require 'lib/functions.php';
require 'lib/visual.inc.php';

// load config-variables
$added_configs = [];
StudipFileloader::load(
    'config_defaults.inc.php config_local.inc.php',
    $added_configs,
    compact('STUDIP_BASE_PATH', 'ABSOLUTE_URI_STUDIP', 'ASSETS_URL', 'CANONICAL_RELATIVE_PATH_STUDIP'),
    true
);

foreach ($added_configs as $key => $value) {
    $GLOBALS[$key] = $value;
}

$config = Symfony\Component\Yaml\Yaml::parseFile(__DIR__ . '/../unit.suite.yml');

// connect to database if configured
if (isset($config['modules']['config']['Db'])) {
    DBManager::getInstance()->setConnection(
        'studip',
        $config['modules']['config']['Db']['dsn'],
        $config['modules']['config']['Db']['user'],
        $config['modules']['config']['Db']['password']
    );
}

// Disable caching to fallback to memory cache
$GLOBALS['CACHING_ENABLE'] = false;

// SimpleORMapFake
if (!class_exists('StudipTestHelper')) {
    class StudipTestHelper
    {
        static function set_up_tables($tables)
        {
            // first step, set fake cache
            $cache = \Studip\Cache\Factory::getCache(false);

            // second step, expire table scheme
            SimpleORMap::expireTableScheme();

            $schemes = [];

            foreach ($tables as $db_table) {
                include TEST_FIXTURES_PATH . "simpleormap/$db_table.php";
                $db_fields = $pk = [];
                foreach ($result as $rs) {
                    $db_fields[mb_strtolower($rs['name'])] = [
                        'name' => $rs['name'],
                        'null' => $rs['null'],
                        'default' => $rs['default'],
                        'type' => $rs['type'],
                        'extra' => $rs['extra'],
                    ];
                    if ($rs['key'] == 'PRI') {
                        $pk[] = mb_strtolower($rs['name']);
                    }
                }
                $schemes[$db_table]['db_fields'] = $db_fields;
                $schemes[$db_table]['pk'] = $pk;
            }

            $cache->write('DB_TABLE_SCHEMES', serialize($schemes));
        }

        static function tear_down_tables()
        {
            SimpleORMap::expireTableScheme();
        }
    }
}
