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
    const VERSION = '1.0.0-RC1';

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
     * Unsets the global container.
     */
    public static function unsetContainer()
    {
        static::$container = null;
    }

    /**
     * Returns the currently active global container.
     *
     * @return \League\Container\ContainerInterface|null
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
    public static function createDefaultContainer($input = null, $output = null)
    {
        // Set up our dependency injection container.
        $container = new Container();
        static::configureContainer($container, $input, $output);
        static::setContainer($container);

        return $container;
    }

    /**
     * Initialize a container with all of the default Robo services.
     */
    public static function configureContainer($container, $input = null, $output = null)
    {
        // Self-referential container refernce for the inflector
        $container->add('container', $container);

        // Create default input and output objects if they were not provided
        if (!$input) {
            $input = new StringInput('');
        }
        if (!$output) {
            $output = new \Symfony\Component\Console\Output\ConsoleOutput();
        }
        $container->add('input', $input);
        $container->add('output', $output);

        // Register logging and related services.
        $container->share('config', \Robo\Config::class);
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
            ->withMethodCall('setFormatterManager', ['formatterManager']);
        $container->share('commandFactory', \Consolidation\AnnotatedCommand\AnnotatedCommandFactory::class)
            ->withMethodCall('setCommandProcessor', ['commandProcessor']);
        $container->add('collectionBuilder', \Robo\Collection\CollectionBuilder::class);
        $container->share('application', \Robo\Application::class)
            ->withArgument('Robo')
            ->withArgument(self::VERSION)
            ->withMethodCall('setAutoExit', [false])
            ->withMethodCall('setDispatcher', ['eventDispatcher']);

        static::addInflectors($container);
        static::addServiceProviders($container, static::standardServices());
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
        $container->inflector(\Robo\Contract\ProgressIndicatorAwareInterface::class)
            ->invokeMethod('setProgressIndicator', ['progressIndicator']);
    }

    /**
     * Register our service providers
     */
    public static function addServiceProviders($container, $providerList)
    {
        foreach ($providerList as $provider) {
            $container->addServiceProvider($provider);
        }
    }

    /**
     * Return the Robo built-in task services
     *
     * @return SimpleServiceProvider[]
     */
    public static function standardServices()
    {
        return
        [
            \Robo\Collection\loadTasks::getCollectionServices(),
            \Robo\Task\ApiGen\loadTasks::getApiGenServices(),
            \Robo\Task\Archive\loadTasks::getArchiveServices(),
            \Robo\Task\Assets\loadTasks::getAssetsServices(),
            \Robo\Task\Base\loadTasks::getBaseServices(),
            \Robo\Task\Npm\loadTasks::getNpmServices(),
            \Robo\Task\Bower\loadTasks::getBowerServices(),
            \Robo\Task\Gulp\loadTasks::getGulpServices(),
            \Robo\Task\Composer\loadTasks::getComposerServices(),
            \Robo\Task\Development\loadTasks::getDevelopmentServices(),
            \Robo\Task\Docker\loadTasks::getDockerServices(),
            \Robo\Task\File\loadTasks::getFileServices(),
            \Robo\Task\Filesystem\loadTasks::getFilesystemServices(),
            \Robo\Task\Remote\loadTasks::getRemoteServices(),
            \Robo\Task\Testing\loadTasks::getTestingServices(),
            \Robo\Task\Vcs\loadTasks::getVcsServices(),
        ];
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
