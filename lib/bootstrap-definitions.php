<?php

use DebugBar\DataCollector\ExceptionsCollector;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\RequestDataCollector;
use DebugBar\DataCollector\TimeDataCollector;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
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

    Studip\Session\Manager::class =>  DI\factory(function () {
        if (Config::get()->CACHING_ENABLE && $GLOBALS['CACHE_IS_SESSION_STORAGE']) {
            $session_handler = new Studip\Session\CacheSessionHandler($GLOBALS['SESSION_OPTIONS']['lifetime'] ?? null);
        } else {
            $session_handler = new Studip\Session\DbSessionHandler();
        }
        $GLOBALS['SESSION_OPTIONS']['path'] = $GLOBALS['CANONICAL_RELATIVE_PATH_STUDIP'];
        $GLOBALS['SESSION_OPTIONS']['secure'] = Request::protocol() === 'https';

        return new Studip\Session\Manager($session_handler, $GLOBALS['SESSION_OPTIONS']);

    }),
    Studip\Authentication\Manager::class => DI\create(),

    // PSR-17 HTTP Factories
    \Psr\Http\Message\RequestFactoryInterface::class => DI\get(Psr17Factory::class),
    \Psr\Http\Message\ResponseFactoryInterface::class => DI\get(Psr17Factory::class),
    \Psr\Http\Message\ServerRequestFactoryInterface::class => DI\get(Psr17Factory::class),
    \Psr\Http\Message\StreamFactoryInterface::class => DI\get(Psr17Factory::class),
    \Psr\Http\Message\UploadedFileFactoryInterface::class => DI\get(Psr17Factory::class),
    \Psr\Http\Message\UriFactoryInterface::class => DI\get(Psr17Factory::class),

    \Psr\Http\Message\ServerRequestInterface::class => DI\factory([ServerRequestCreator::class, 'fromGlobals']),
];
