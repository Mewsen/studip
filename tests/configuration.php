<?php

return (function (string ...$filenames) {
    foreach ($filenames as $filename) {
        if (file_exists($filename)) {
            require_once $filename;
        }
    }

    return array_filter([
        'DB_STUDIP_HOST'     => $DB_STUDIP_HOST ?? null,
        'DB_STUDIP_USER'     => $DB_STUDIP_USER ?? null,
        'DB_STUDIP_PASSWORD' => $DB_STUDIP_PASSWORD ?? null,
        'DB_STUDIP_DATABASE' => $DB_STUDIP_DATABASE ?? null,
    ]);
})(
    dirname(__DIR__).'/config/config_defaults.inc.php',
    dirname(__DIR__).'/config/config_local.inc.php'
);
