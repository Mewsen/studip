<?php
define ('Studip\\ENV', $_ENV['ENV'] ?? 'development');

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

/*URL
----------------------------------------------------------------
customize if automatic detection fails, e.g. when installation is hidden
behind a proxy
*/
//$CANONICAL_RELATIVE_PATH_STUDIP = '/';
//$ABSOLUTE_URI_STUDIP = 'https://www.studip.de/';
//$ASSETS_URL = 'https://www.studip.de/assets/';

// Set proxy url
if (isset($_ENV['PROXY_URL'])) {
    $ABSOLUTE_URI_STUDIP = $_ENV['PROXY_URL'];
    $ASSETS_URL = $_ENV['PROXY_URL'].'/assets/';
}

// Use autoproxy
if (isset($_ENV['AUTO_PROXY'])) {
    $ABSOLUTE_URI_STUDIP = $_SERVER['HTTP_X_FORWARDED_PROTO'].'://'.$_SERVER['HTTP_X_FORWARDED_HOST'].'/';
    $ASSETS_URL = $ABSOLUTE_URI_STUDIP.'/assets/';
}

$CONTENT_LANGUAGES['en_GB'] = ['picture' => 'lang_en.gif', 'name' => 'English'];
