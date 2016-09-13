<?php
namespace Robo;

use League\Container\Container;
use League\Container\ContainerInterface;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\StringInput;

/**
 * Manages the container reference and other static data.  Favor
 * using dependency injection wherever possible.  Avoid using
 * this class directly, unless setting up a custom DI container.
 */
class Robo
{
    const APPLICATION_NAME = 'Robo';
    const VERSION = '1.0.0-RC3';

    /**
     * The currently active container object, or NULL if not initialized yet.
     *
     * @var ContainerInterface|null
     */
    protected static $container;

    /**
     * Entrypoint for standalone Robo-based tools.  See docs/framework.md.
     */
    public static function run($argv, $commandClasses, $appName = null, $appVersion = null, $output = null)
    {
        $runner = new \Robo\Runner($commandClasses);
        $statusCode = $runner->execute($argv, $appName, $appVersion, $output);
        return $statusCode;
    }

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
     * Unsets the global container.
     */
    public static function unsetContainer()
    {
        static::$container = null;
    }

    /**
     * Returns the currently active global container.
     *
     * @return \League\Container\ContainerInterface
     *
     * @throws \RuntimeException
     */
    public static function getContainer()
    {
        if (static::$container === null) {
            throw new \RuntimeException('container is not initialized yet. \Robo\Robo::setContainer() must be called with a real container.');
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
     * Create a container and initiailze it.
     */
    public static function createDefaultContainer($input = null, $output = null, $app = null)
    {
        // Do not allow this function to be called more than once.
        if (static::hasContainer()) {
            return static::getContainer();
        }

        // Set up our dependency injection container.
        $container = new Container();
        $config = new Config();
        static::configureContainer($container, $config, $input, $output, $app);

        return $container;
    }

    /**
     * Initialize a container with all of the default Robo services.
     */
    public static function configureContainer(ContainerInterface $container, Config $config, $input = null, $output = null, $app = null)
    {
        // Self-referential container refernce for the inflector
        $container->add('container', $container);
        static::setContainer($container);

        // Create default input and output objects if they were not provided
        if (!$input) {
            $input = new StringInput('');
        }
        if (!$output) {
            $output = new \Symfony\Component\Console\Output\ConsoleOutput();
        }
        $config->setDecorated($output->isDecorated());
        $container->share('input', $input);
        $container->share('output', $output);
        $container->share('config', $config);

        // Register logging and related services.
        $container->share('logStyler', \Robo\Log\RoboLogStyle::class);
        $container->share('logger', \Robo\Log\RoboLogger::class)
            ->withArgument('output')
            ->withMethodCall('setLogOutputStyler', ['logStyler']);
        $container->add('progressBar', \Symfony\Component\Console\Helper\ProgressBar::class)
            ->withArgument('output');
        $container->share('progressIndicator', \Robo\Common\ProgressIndicator::class)
            ->withArgument('progressBar')
            ->withArgument('output');
        $container->share('resultPrinter', \Robo\Log\ResultPrinter::class);
        $container->add('simulator', \Robo\Task\Simulator::class);
        $container->share('globalOptionsEventListener', \Robo\GlobalOptionsEventListener::class);
        $container->share('eventDispatcher', \Symfony\Component\EventDispatcher\EventDispatcher::class)
            ->withMethodCall('addSubscriber', ['globalOptionsEventListener']);
        $container->share('collectionProcessHook', \Robo\Collection\CollectionProcessHook::class);
        $container->share('hookManager', \Consolidation\AnnotatedCommand\Hooks\HookManager::class)
            ->withMethodCall('addResultProcessor', ['collectionProcessHook', '*']);
        $container->share('formatterManager', \Consolidation\OutputFormatters\FormatterManager::class);
        $container->share('commandProcessor', \Consolidation\AnnotatedCommand\CommandProcessor::class)
            ->withArgument('hookManager')
            ->withMethodCall('setFormatterManager', ['formatterManager'])
            ->withMethodCall(
                'setDisplayErrorFunction',
                [
                    function ($output, $message) use ($container) {
                        $logger = $container->get('logger');
                        $logger->error($message);
                    }
                ]
            );
        $container->share('commandFactory', \Consolidation\AnnotatedCommand\AnnotatedCommandFactory::class)
            ->withMethodCall('setCommandProcessor', ['commandProcessor']);
        $container->add('collection', \Robo\Collection\Collection::class);
        $container->add('collectionBuilder', \Robo\Collection\CollectionBuilder::class);
        static::addInflectors($container);

        if (!$app) {
            $app = static::createDefaultApplication();
        }
        $app->setAutoExit(false);
        $app->setDispatcher($container->get('eventDispatcher'));
        $container->share('application', $app);
    }

    public static function createDefaultApplication($appName = null, $appVersion = null)
    {
        $appName = $appName ?: self::APPLICATION_NAME;
        $appVersion = $appVersion ?: self::VERSION;

        $app = new \Robo\Application($appName, $appVersion);
        $app->setAutoExit(false);
        return $app;
    }

    /**
     * Add the Robo League\Container inflectors to the container
     */
    public static function addInflectors($container)
    {
        // Register our various inflectors.
        $container->inflector(\Robo\Contract\ConfigAwareInterface::class)
            ->invokeMethod('setConfig', ['config']);
        $container->inflector(\Psr\Log\LoggerAwareInterface::class)
            ->invokeMethod('setLogger', ['logger']);
        $container->inflector(\League\Container\ContainerAwareInterface::class)
            ->invokeMethod('setContainer', ['container']);
        $container->inflector(\Symfony\Component\Console\Input\InputAwareInterface::class)
            ->invokeMethod('setInput', ['input']);
        $container->inflector(\Robo\Contract\OutputAwareInterface::class)
            ->invokeMethod('setOutput', ['output']);
        $container->inflector(\Robo\Contract\ProgressIndicatorAwareInterface::class)
            ->invokeMethod('setProgressIndicator', ['progressIndicator']);
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
     * Return the result printer object.
     *
     * @return ResultPrinter
     */
    public static function resultPrinter()
    {
        return static::service('resultPrinter');
    }

    public static function config()
    {
        return static::service('config');
    }

    public static function logger()
    {
        return static::service('logger');
    }

    public static function application()
    {
        return static::service('application');
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
}
