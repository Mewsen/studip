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
use OAT\Library\Lti1p3Ags\Repository\LineItemRepositoryInterface;
use OAT\Library\Lti1p3Core\Message\Launch\Validator\Platform\PlatformLaunchValidator;
use OAT\Library\Lti1p3Core\Message\Launch\Validator\Platform\PlatformLaunchValidatorInterface;
use OAT\Library\Lti1p3Core\Message\Launch\Validator\Tool\ToolLaunchValidator;
use OAT\Library\Lti1p3Core\Message\Launch\Validator\Tool\ToolLaunchValidatorInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationRepositoryInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainRepository;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainRepositoryInterface;
use OAT\Library\Lti1p3Core\Security\Nonce\NonceRepository;
use OAT\Library\Lti1p3Core\Security\Nonce\NonceRepositoryInterface;
use OAT\Library\Lti1p3Core\Security\OAuth2\Validator\RequestAccessTokenValidator;
use OAT\Library\Lti1p3Core\Security\OAuth2\Validator\RequestAccessTokenValidatorInterface;
use OAT\Library\Lti1p3Core\Security\User\UserAuthenticatorInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use Studip\Cache\Factory as CacheFactory;
use Studip\Lti\LTI1p3\LineItemRepository;
use Studip\Lti\LTI1p3\PlatformManager;
use Studip\Lti\LTI1p3\RegistrationManager;
use Studip\Lti\LTI1p3\ToolManager;

use Studip\Lti\LTI1p3\UserAuthenticator;

use function DI\create;

return [
    \Flexi\Factory::class => DI\factory(function () {
        return new \Flexi\Factory("{$GLOBALS['STUDIP_BASE_PATH']}/templates");
    }),
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

    // LTI
    RegistrationRepositoryInterface::class => DI\get(RegistrationManager::class),
    NonceRepositoryInterface::class => DI\get(NonceRepository::class),
    PlatformLaunchValidatorInterface::class => DI\get(PlatformLaunchValidator::class),
    ToolLaunchValidatorInterface::class => DI\get(ToolLaunchValidator::class),
    UserAuthenticatorInterface::class => DI\get(UserAuthenticator::class),
    CacheItemPoolInterface::class => DI\factory(fn() => CacheFactory::getCache()),
    LineItemRepositoryInterface::class => DI\factory(fn() => LineItemRepository::class),
    RequestAccessTokenValidatorInterface::class => DI\factory(fn() => RequestAccessTokenValidator::class),
    KeyChainRepositoryInterface::class => DI\factory(function() {
        return new KeyChainRepository([
            PlatformManager::getKeyring()->toKeyChain(),
            ToolManager::getKeyring()->toKeyChain()
        ]);
    }),
];
