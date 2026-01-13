<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php84\Rector\Param\ExplicitNullableParamTypeRector;

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
    ->withPhpVersion(Rector\ValueObject\PhpVersion::PHP_84)
    ->withRules([
        ExplicitNullableParamTypeRector::class,
    ]);
