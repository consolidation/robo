<?php
namespace Robo;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\StringInput;

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
    public static function setContainer(ContainerInterface $container)
    {
        static::$container = $container;
    }

    /**
     * Create a container and initiailze it.
     */
    public static function createContainer($input = null)
    {
        // If no input was provided, then create an empty input object.
        if (!$input) {
            $input = new StringInput('');
        }

        // Set up our dependency injection container.
        $container = new ContainerBuilder();
        $container->register('logStyler', 'Robo\Common\RoboLogStyle');
        $container->set('input', $input);
        $container
            ->register('output', 'Symfony\Component\Console\Output\ConsoleOutput');
        $container
            ->register('logger', 'Robo\Common\RoboLogger')
            ->addArgument(new Reference('output'))
            ->addMethodCall('setLogOutputStyler', array(new Reference('logStyler')));

        return $container;
    }

    /**
     * Unsets the global container.
     */
    public static function unsetContainer()
    {
        static::$container = null;
    }

    /**
     * Returns the currently active global container.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface|null
     *
     * @throws \Drupal\Core\DependencyInjection\ContainerNotInitializedException
     */
    public static function getContainer()
    {
        if (static::$container === null) {
            throw new \RuntimeException('container is not initialized yet. \Robo\Config::setContainer() must be called with a real container.');
        }
        return static::$container;
    }

    /**
     * Returns TRUE if the container has been initialized, FALSE otherwise.
     *
     * @return bool
     */
    public static function hasContainer()
    {
        return static::$container !== null;
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
    public static function service($id)
    {
        return static::getContainer()->get($id);
    }

    /**
     * Add a service to the container.
     */
    public static function setService($id, $service)
    {
        static::getContainer()->set($id, $service);
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
    public static function hasService($id)
    {
        // Check hasContainer() first in order to always return a Boolean.
        return static::hasContainer() && static::getContainer()->has($id);
    }

    /**
     * Return the logger object.
     *
     * @return LoggerInterface
     */
    public static function logger()
    {
        return static::service('logger');
    }

    /**
     * Return the output object.
     *
     * @return OutputInterface
     */
    public static function output()
    {
        return static::service('output');
    }

    /**
     * Return the input object.
     *
     * @return InputInterface
     */
    public static function input()
    {
        return static::service('input');
    }

    public static function setOutput(OutputInterface $output)
    {
        static::setService('output', $output);
    }

    public static function setInput(InputInterface $input)
    {
        static::setService('input', $input);
    }

    public static function get($key, $default = null)
    {
        if (!static::$container->hasParameter($key)) {
            return $default;
        }
        return static::$container->getParameter($key);
    }

    public static function set($key, $value)
    {
        static::$container->setParameter($key, $value);
    }
}
