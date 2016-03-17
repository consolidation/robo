<?php
namespace Robo;

use Robo\Config;
use Robo\Common\IO;
use Robo\Container\RoboContainer;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class Runner
{
    use IO;

    const VERSION = '0.6.1';
    const ROBOCLASS = 'RoboFile';
    const ROBOFILE = 'RoboFile.php';

    /**
     * @var string PassThoughArgs
     */
    protected $passThroughArgs = null;

    /**
     * @var string RoboClass
     */
    protected $roboClass;

    /**
     * @var string RoboFile
     */
    protected $roboFile;

    /**
     * @var string working dir of Robo
     */
    protected $dir;

    /**
     * Class Constructor
     * @param null $roboClass
     * @param null $roboFile
     */
    public function __construct($roboClass = null, $roboFile = null, $container = null)
    {
        // set the const as class properties to allow overwriting in child classes
        $this->roboClass = $roboClass ? $roboClass : self::ROBOCLASS ;
        $this->roboFile  = $roboFile ? $roboFile : self::ROBOFILE;
        $this->dir = getcwd();

        // Store the container in our config object if it was provided.
        if ($container != null) {
            Config::setContainer($container);
        }
    }

    protected function loadRoboFile()
    {
        if (!file_exists($this->dir)) {
            $this->yell("Path in `{$this->dir}` is invalid, please provide valid absolute path to load Robofile", 40, 'red');
            return false;
        }

        $this->dir = realpath($this->dir);
        chdir($this->dir);

        if (!file_exists($this->dir . DIRECTORY_SEPARATOR . $this->roboFile)) {
            return false;
        }

        require_once $this->dir . DIRECTORY_SEPARATOR .$this->roboFile;

        if (!class_exists($this->roboClass)) {
            $this->writeln("<error>Class ".$this->roboClass." was not loaded</error>");
            return false;
        }
        return true;
    }

    public function execute($input = null)
    {
        register_shutdown_function(array($this, 'shutdown'));
        set_error_handler(array($this, 'handleError'));

        // If we were not provided a container, then create one
        if (!Config::hasContainer()) {
            $input = $this->prepareInput($input ? $input : $_SERVER['argv']);
            // Set up our dependency injection container.
            $container = new RoboContainer();
            static::configureContainer($container, $input);
            static::addServiceProviders($container);
            $container->share('application', \Robo\Application::class)
                ->withArgument('Robo')
                ->withArgument(self::VERSION);
            Config::setContainer($container);
        }

        $container = Config::getContainer();
        $app = $container->get('application');

        if (!$this->loadRoboFile()) {
            $this->yell("Robo is not initialized here. Please run `robo init` to create a new RoboFile", 40, 'yellow');
            $app->addInitRoboFileCommand($this->roboFile, $this->roboClass);
            $app->run(Config::input(), Config::output());
            return;
        }
        $app->addCommandsFromClass($this->roboClass, $this->passThroughArgs);
        $app->run($container->get('input'), $container->get('output'));
    }

    /**
     * Create a container and initiailze it.
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
        $container->share('logStyler', \Robo\Log\RoboLogStyle::class);
        $container->share('logger', \Robo\Log\RoboLogger::class)
            ->withArgument('output')
            ->withMethodCall('setLogOutputStyler', ['logStyler']);
        $container->share('resultPrinter', \Robo\Log\ResultPrinter::class);
        $container->add('simulator', \Robo\Task\Simulator::class);

        // Register our various inflectors.
        $container->inflector(\Psr\Log\LoggerAwareInterface::class)
            ->invokeMethod('setLogger', ['logger']);
        $container->inflector(\League\Container\ContainerAwareInterface::class)
            ->invokeMethod('setContainer', ['container']);
        $container->inflector(\Symfony\Component\Console\Input\InputAwareInterface::class)
            ->invokeMethod('setInput', ['input']);
    }

    /**
     * Register our service providers
     */
    public static function addServiceProviders($container)
    {
        $container->addServiceProvider(\Robo\Collection\Collection::getCollectionServices());
        $container->addServiceProvider(\Robo\Task\ApiGen\loadTasks::getApiGenServices());
        $container->addServiceProvider(\Robo\Task\Archive\loadTasks::getArchiveServices());
        $container->addServiceProvider(\Robo\Task\Assets\loadTasks::getAssetsServices());
        $container->addServiceProvider(\Robo\Task\Base\loadTasks::getBaseServices());
        $container->addServiceProvider(\Robo\Task\Bower\loadTasks::getBowerServices());
        $container->addServiceProvider(\Robo\Task\Composer\loadTasks::getComposerServices());
        $container->addServiceProvider(\Robo\Task\Development\loadTasks::getDevelopmentServices());
        $container->addServiceProvider(\Robo\Task\Docker\loadTasks::getDockerServices());
        $container->addServiceProvider(\Robo\Task\File\loadTasks::getFileServices());
        $container->addServiceProvider(\Robo\Task\FileSystem\loadTasks::getFileSystemServices());
        $container->addServiceProvider(\Robo\Task\Remote\loadTasks::getRemoteServices());
        $container->addServiceProvider(\Robo\Task\Testing\loadTasks::getTestingServices());
        $container->addServiceProvider(\Robo\Task\Vcs\loadTasks::getVcsServices());
    }

    /**
     * @param $argv
     * @return ArgvInput
     */
    protected function prepareInput($argv)
    {
        $pos = array_search('--', $argv);

        // cutting pass-through arguments
        if ($pos !== false) {
            $this->passThroughArgs = implode(' ', array_slice($argv, $pos+1));
            $argv = array_slice($argv, 0, $pos);
        }

        // loading from other directory
        $pos = array_search('--load-from', $argv);
        if ($pos !== false) {
            if (isset($argv[$pos +1])) {
                $this->dir = $argv[$pos +1];
                unset($argv[$pos +1]);
            }
            unset($argv[$pos]);
        }
        return new ArgvInput($argv);
    }

    public function shutdown()
    {
        $error = error_get_last();
        if (!is_array($error)) return;
        $this->writeln(sprintf("<error>ERROR: %s \nin %s:%d\n</error>", $error['message'], $error['file'], $error['line']));
    }

    /**
     * This is just a proxy error handler that checks the current error_reporting level.
     * In case error_reporting is disabled the error is marked as handled, otherwise
     * the normal internal error handling resumes.
     *
     * @return bool
     */
    public function handleError()
    {
        if (error_reporting() === 0) {
            return true;
        }
        return false;
    }
}

