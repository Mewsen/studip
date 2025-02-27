<?php

namespace Studip;

use DI\Container;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

class DIContainer
{
    /**
     * The current globally available container.
     */
    protected static ?Container $instance = null;

    /**
     * Get the globally available instance of the container.
     *
     * @return Container
     * @throws \Exception
     */
    public static function getInstance(): Container
    {
        if (static::$instance === null) {
            $builder = static::createBuilder();
            static::$instance = $builder->build();
        }

        return static::$instance;
    }

    /**
     * Set the instance of the container.
     *
     * @param ContainerInterface|null $container
     * @return ContainerInterface
     */
    public static function setInstance(?ContainerInterface $container = null): ContainerInterface
    {
        return static::$instance = $container;
    }

    /**
     * Set up the ContainerBuilder.
     */
    protected static function createBuilder(): ContainerBuilder
    {
        $builder = new ContainerBuilder();
        if (ENV === 'production') {
            $builder->enableCompilation(
                self::getCompilationPath(),
                self::getCompilationClass()
            );
        }
        $builder->useAttributes(true);
        $builder->addDefinitions('lib/bootstrap-definitions.php');

        $jsonapiSettings = require 'lib/classes/JsonApi/settings.php';
        $jsonapiSettings($builder);

        $jsonapiDependencies = require 'lib/classes/JsonApi/dependencies.php';
        $jsonapiDependencies($builder);

        return $builder;
    }

    /**
     * Returns the path to the compiled container
     */
    public static function getCompilationPath(): string
    {
        return $GLOBALS['TMP_PATH'];
    }

    /**
     * Returns the class of the compiled container
     */
    public static function getCompilationClass(): string
    {
        return 'CompiledContainer';
    }
}
