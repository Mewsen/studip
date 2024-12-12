<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app/controllers',
        __DIR__ . '/cli',
        __DIR__ . '/config',
        __DIR__ . '/db',
        __DIR__ . '/lib',
        __DIR__ . '/public/*.php',
        __DIR__ . '/tests',
    ])
    ->withSkip([
        __DIR__ . '/tests/_data',
        __DIR__ . '/tests/_support',
    ])
    ->withSets([
        __DIR__ . '/lib/Rectors/Studip-6.0-Set.php'
    ])
    // uncomment to reach your current PHP version
    ->withPhpSets()
//    ->withTypeCoverageLevel(0)
//    ->withDeadCodeLevel(0)
//    ->withCodeQualityLevel(0)
;
