<?php
namespace Robo;

use League\Container\Container;
use League\Container\ContainerInterface;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\StringInput;

class Config
{
    protected static $simulated;
    protected static $config = [];

    /**
     * The currently active container object, or NULL if not initialized yet.
     *
     * @var ContainerInterface|null
     */
    protected static $container;

    /**
     * Sets a new global container.
     *
     * @param ContainerInterface $container
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
        $container = new Container();

        $container->add('input', $input);
        $container->share('output', 'Symfony\Component\Console\Output\ConsoleOutput');
        $container->share('logStyler', 'Robo\Log\RoboLogStyle');
        $container->share('logger', 'Robo\Log\RoboLogger')
            ->withArgument('output')
            ->withMethodCall('setLogOutputStyler', ['logStyler']);
        $container->share('resultPrinter', 'Robo\Log\ResultPrinter');
        $container->share('taskAssembler', 'Robo\TaskAssembler');

        $container->inflector('Psr\Log\LoggerAwareInterface')
            ->invokeMethod('setLogger', ['logger']);

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
        static::getContainer()->add($id, $service);
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
     * Return the result printer object.
     *
     * @return ResultPrinter
     */
    public static function resultPrinter()
    {
        return static::service('resultPrinter');
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
        return isset(self::$config[$key]) ? self::$config[$key] : $default;
    }

    public static function set($key, $value)
    {
        self::$config[$key] = $value;
    }

    public static function setGlobalOptions($input)
    {
        static::$simulated = $input->getOption('simulate');
    }

    public static function isSimulated()
    {
        return static::$simulated;
    }
}
