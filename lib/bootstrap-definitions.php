<?php

use DebugBar\DataCollector\ExceptionsCollector;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\RequestDataCollector;
use DebugBar\DataCollector\TimeDataCollector;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use function DI\create;

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
    PDO::class => DI\factory(function () {
        return DBManager::get();
    }),
    Trails\Dispatcher::class => DI\factory(function (ContainerInterface $container) {
        return new \StudipDispatcher($container);
    }),
    DebugBar\DebugBar::class => DI\factory(function (ContainerInterface $container) {
        $debugBar = new DebugBar\DebugBar();
        $debugBar->addCollector(new PhpInfoCollector());
        $debugBar->addCollector(new RequestDataCollector());
        $debugBar->addCollector(new MemoryCollector());
        $debugBar->addCollector(new ExceptionsCollector());

        // Future Improvements, not used/activated right now
        # $debugBar->addCollector(new MessagesCollector());
        $debugBar->addCollector(new TimeDataCollector());

        $config = iterator_to_array(Config::getInstance()->getIterator());
        ksort($config);
        $debugBar->addCollector(new DebugBar\DataCollector\ConfigCollector($config));

        $pdo = $container->get(PDO::class);
        if ($pdo instanceof Studip\Debug\TraceableStudipPDO) {
            $collector = new DebugBar\DataCollector\PDO\PDOCollector($pdo);
            $debugBar->addCollector($collector);
        }

        return $debugBar;
    }),
    StudipPDO::class => DI\factory(function () {
        $pdo = new StudipPDO(
            "mysql:host={$GLOBALS['DB_STUDIP_HOST']};dbname={$GLOBALS['DB_STUDIP_DATABASE']};charset=utf8mb4",
            $GLOBALS['DB_STUDIP_USER'],
            $GLOBALS['DB_STUDIP_PASSWORD']
        );

        if (Studip\Debug\DebugBar::isActivated()) {
            $pdo = new Studip\Debug\TraceableStudipPDO($pdo);
        }

        return $pdo;
    }),
    PluginManager::class => DI\factory([PluginManager::class, 'getInstance']),
];
