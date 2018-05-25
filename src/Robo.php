<?php
namespace Robo;

use Composer\Autoload\ClassLoader;
use League\Container\Container;
use League\Container\ContainerInterface;
use Robo\Common\ProcessExecutor;
use Consolidation\Config\ConfigInterface;
use Consolidation\Config\Loader\ConfigProcessor;
use Consolidation\Config\Loader\YamlConfigLoader;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Process\Process;

/**
 * Manages the container reference and other static data.  Favor
 * using dependency injection wherever possible.  Avoid using
 * this class directly, unless setting up a custom DI container.
 */
class Robo
{
    const APPLICATION_NAME = 'Robo';
    const VERSION = '1.2.4';

    /**
     * The currently active container object, or NULL if not initialized yet.
     *
     * @var ContainerInterface|null
     */
    protected static $container;

    /**
     * Entrypoint for standalone Robo-based tools.  See docs/framework.md.
     *
     * @param string[] $argv
     * @param string $commandClasses
     * @param null|string $appName
     * @param null|string $appVersion
     * @param null|\Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public static function run($argv, $commandClasses, $appName = null, $appVersion = null, $output = null, $repository = null)
    {
        $runner = new \Robo\Runner($commandClasses);
        $runner->setSelfUpdateRepository($repository);
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
     * Create a config object and load it from the provided paths.
     */
    public static function createConfiguration($paths)
    {
        $config = new \Robo\Config\Config();
        static::loadConfiguration($paths, $config);
        return $config;
    }

    /**
     * Use a simple config loader to load configuration values from specified paths
     */
    public static function loadConfiguration($paths, $config = null)
    {
        if ($config == null) {
            $config = static::config();
        }
        $loader = new YamlConfigLoader();
        $processor = new ConfigProcessor();
        $processor->add($config->export());
        foreach ($paths as $path) {
            $processor->extend($loader->load($path));
        }
        $config->import($processor->export());
    }

    /**
     * Create a container and initiailze it.  If you wish to *change*
     * anything defined in the container, then you should call
     * \Robo::configureContainer() instead of this function.
     *
     * @param null|\Symfony\Component\Console\Input\InputInterface $input
     * @param null|\Symfony\Component\Console\Output\OutputInterface $output
     * @param null|\Robo\Application $app
     * @param null|ConfigInterface $config
     * @param null|\Composer\Autoload\ClassLoader $classLoader
     *
     * @return \League\Container\Container|\League\Container\ContainerInterface
     */
    public static function createDefaultContainer($input = null, $output = null, $app = null, $config = null, $classLoader = null)
    {
        // Do not allow this function to be called more than once.
        if (static::hasContainer()) {
            return static::getContainer();
        }

        if (!$app) {
            $app = static::createDefaultApplication();
        }

        if (!$config) {
            $config = new \Robo\Config\Config();
        }

        // Set up our dependency injection container.
        $container = new Container();
        static::configureContainer($container, $app, $config, $input, $output, $classLoader);

        // Set the application dispatcher
        $app->setDispatcher($container->get('eventDispatcher'));

        return $container;
    }

    /**
     * Initialize a container with all of the default Robo services.
     * IMPORTANT:  after calling this method, clients MUST call:
     *
     * $dispatcher = $container->get('eventDispatcher');
     * $app->setDispatcher($dispatcher);
     *
     * Any modification to the container should be done prior to fetching
     * objects from it.
     *
     * It is recommended to use \Robo::createDefaultContainer()
     * instead, which does all required setup for the caller, but has
     * the limitation that the container it creates can only be
     * extended, not modified.
     *
     * @param \League\Container\ContainerInterface $container
     * @param \Symfony\Component\Console\Application $app
     * @param ConfigInterface $config
     * @param null|\Symfony\Component\Console\Input\InputInterface $input
     * @param null|\Symfony\Component\Console\Output\OutputInterface $output
     * @param null|\Composer\Autoload\ClassLoader $classLoader
     */
    public static function configureContainer(ContainerInterface $container, SymfonyApplication $app, ConfigInterface $config, $input = null, $output = null, $classLoader = null)
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
        if (!$classLoader) {
            $classLoader = new ClassLoader();
        }
        $config->set(Config::DECORATED, $output->isDecorated());
        $config->set(Config::INTERACTIVE, $input->isInteractive());

        $container->share('application', $app);
        $container->share('config', $config);
        $container->share('input', $input);
        $container->share('output', $output);
        $container->share('outputAdapter', \Robo\Common\OutputAdapter::class);
        $container->share('classLoader', $classLoader);

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
        $container->share('globalOptionsEventListener', \Robo\GlobalOptionsEventListener::class)
            ->withMethodCall('setApplication', ['application']);
        $container->share('injectConfigEventListener', \Consolidation\Config\Inject\ConfigForCommand::class)
            ->withArgument('config')
            ->withMethodCall('setApplication', ['application']);
        $container->share('collectionProcessHook', \Robo\Collection\CollectionProcessHook::class);
        $container->share('alterOptionsCommandEvent', \Consolidation\AnnotatedCommand\Options\AlterOptionsCommandEvent::class)
            ->withArgument('application');
        $container->share('hookManager', \Consolidation\AnnotatedCommand\Hooks\HookManager::class)
            ->withMethodCall('addCommandEvent', ['alterOptionsCommandEvent'])
            ->withMethodCall('addCommandEvent', ['injectConfigEventListener'])
            ->withMethodCall('addCommandEvent', ['globalOptionsEventListener'])
            ->withMethodCall('addResultProcessor', ['collectionProcessHook', '*']);
        $container->share('eventDispatcher', \Symfony\Component\EventDispatcher\EventDispatcher::class)
            ->withMethodCall('addSubscriber', ['hookManager']);
        $container->share('formatterManager', \Consolidation\OutputFormatters\FormatterManager::class)
            ->withMethodCall('addDefaultFormatters', [])
            ->withMethodCall('addDefaultSimplifiers', []);
        $container->share('prepareTerminalWidthOption', \Consolidation\AnnotatedCommand\Options\PrepareTerminalWidthOption::class)
            ->withMethodCall('setApplication', ['application']);
        $container->share('commandProcessor', \Consolidation\AnnotatedCommand\CommandProcessor::class)
            ->withArgument('hookManager')
            ->withMethodCall('setFormatterManager', ['formatterManager'])
            ->withMethodCall('addPrepareFormatter', ['prepareTerminalWidthOption'])
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
        $container->share('relativeNamespaceDiscovery', \Robo\ClassDiscovery\RelativeNamespaceDiscovery::class)
            ->withArgument('classLoader');

        // Deprecated: favor using collection builders to direct use of collections.
        $container->add('collection', \Robo\Collection\Collection::class);
        // Deprecated: use CollectionBuilder::create() instead -- or, better
        // yet, BuilderAwareInterface::collectionBuilder() if available.
        $container->add('collectionBuilder', \Robo\Collection\CollectionBuilder::class);

        static::addInflectors($container);

        // Make sure the application is appropriately initialized.
        $app->setAutoExit(false);
    }

    /**
     * @param null|string $appName
     * @param null|string $appVersion
     *
     * @return \Robo\Application
     */
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
     *
     * @param \League\Container\ContainerInterface $container
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
        $container->inflector(\Consolidation\AnnotatedCommand\Events\CustomEventAwareInterface::class)
            ->invokeMethod('setHookManager', ['hookManager']);
        $container->inflector(\Robo\Contract\VerbosityThresholdInterface::class)
            ->invokeMethod('setOutputAdapter', ['outputAdapter']);
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
     * @return \Robo\Log\ResultPrinter
     */
    public static function resultPrinter()
    {
        return static::service('resultPrinter');
    }

    /**
     * @return ConfigInterface
     */
    public static function config()
    {
        return static::service('config');
    }

    /**
     * @return \Consolidation\Log\Logger
     */
    public static function logger()
    {
        return static::service('logger');
    }

    /**
     * @return \Robo\Application
     */
    public static function application()
    {
        return static::service('application');
    }

    /**
     * Return the output object.
     *
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    public static function output()
    {
        return static::service('output');
    }

    /**
     * Return the input object.
     *
     * @return \Symfony\Component\Console\Input\InputInterface
     */
    public static function input()
    {
        return static::service('input');
    }

    public static function process(Process $process)
    {
        return ProcessExecutor::create(static::getContainer(), $process);
    }
}
