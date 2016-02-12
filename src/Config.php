<?php
namespace Robo;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Config
{
    /**
     * The currently active container object, or NULL if not initialized yet.
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface|null
     */
    protected static $container;

    /**
     * Sets a new global container.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     *   A new container instance to replace the current.
     */
    public static function setContainer(ContainerInterface $container) {
        static::$container = $container;
    }

    /**
     * Unsets the global container.
     */
    public static function unsetContainer() {
        static::$container = NULL;
    }

    /**
     * Returns the currently active global container.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface|null
     *
     * @throws \Drupal\Core\DependencyInjection\ContainerNotInitializedException
     */
    public static function getContainer() {
        if (static::$container === NULL) {
            throw new RuntimeException('container is not initialized yet. \Robo\Config::setContainer() must be called with a real container.');
        }
        return static::$container;
    }

    /**
     * Returns TRUE if the container has been initialized, FALSE otherwise.
     *
     * @return bool
     */
    public static function hasContainer() {
        return static::$container !== NULL;
    }

    /**
     * Retrieves a service from the container.
     *
     * Use this method if the desired service is not one of those with a dedicated
     * accessor method below. If it is listed below, those methods are preferred
     * as they can return useful type hints.
     *
     * @param string $id
     *   The ID of the service to retrieve.
     *
     * @return mixed
     *   The specified service.
     */
    public static function service($id) {
        return static::getContainer()->get($id);
    }

    /**
     * Indicates if a service is defined in the container.
     *
     * @param string $id
     *   The ID of the service to check.
     *
     * @return bool
     *   TRUE if the specified service exists, FALSE otherwise.
     */
    public static function hasService($id) {
        // Check hasContainer() first in order to always return a Boolean.
        return static::hasContainer() && static::getContainer()->has($id);
    }

    /**
     * Return the logger object.
     *
     * @return LoggerInterface
     */
    public static function logger() {
        return static::service('logger');
    }

    protected static $config = [
        'output' => null,
        'input' => null
    ];

    public static function setOutput(OutputInterface $output)
    {
        self::$config['output'] = $output;
    }

    public static function setInput(InputInterface $input)
    {
        self::$config['input'] = $input;
    }

    public static function get($key, $default = null)
    {
        return isset(self::$config[$key]) ? self::$config[$key] : $default;
    }

    public static function set($key, $value)
    {
        self::$config[$key] = $value;
    }
}
