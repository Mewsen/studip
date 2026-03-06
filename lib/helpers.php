<?php

use Psr\Container\ContainerInterface;

/**
 * This function returns the Dependency Injection container used.
 *
 * ```
 * $container = studipApp();
 * ```
 *
 * You may pass an entry name, a class or interface name to resolve it from the container:
 *
 * ```
 * $logger = studipApp(LoggerInterface::class);
 * ```
 * @template T
 * @param T|class-string<T>|string|null $entryId    entry name or a class name
 * @param array       $parameters Optional parameters to use to build the entry.
 *                                Use this to force specific parameters to specific values.
 *                                Parameters not defined in this array will be resolved using the container.
 *
 * @return T|ContainerInterface|mixed either the DI container or the entry associated to the $entryId
 */
function studipApp($entryId = null, $parameters = [])
{
    $container = \Studip\DIContainer::getInstance();
    if (is_null($entryId)) {
        return $container;
    }

    return $container->make($entryId, $parameters);
}

/**
 * @return \Studip\Session\Manager
 */
function sess() : Studip\Session\Manager
{
    return studipApp()->get(Studip\Session\Manager::class);
}

/**
 * @return \Studip\Authentication\Manager
 */
function auth() : Studip\Authentication\Manager
{
    return studipApp()->get(Studip\Authentication\Manager::class);
}
