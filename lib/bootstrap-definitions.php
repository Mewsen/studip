<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return [
    LoggerInterface::class => DI\factory(function () {
        return new Logger('studip', [
            new StreamHandler(
                $GLOBALS['TMP_PATH'] . '/studip.log',
                \Studip\ENV === 'development' ? Logger::DEBUG : Logger::ERROR
            ),
        ]);
    }),
    \Studip\Cache\Cache::class => DI\factory(function () {
        return \Studip\Cache\Factory::getCache();
    }),
    StudipPDO::class => DI\factory(function () {
        return DBManager::get();
    }),
    Trails\Dispatcher::class => DI\factory(function (ContainerInterface $container) {
        return new \StudipDispatcher($container);
    }),
];
