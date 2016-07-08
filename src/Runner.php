<?php
namespace Robo;

use Robo\Config;
use Robo\Common\IO;
use Robo\Container\RoboContainer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Consolidation\AnnotatedCommand\AnnotatedCommandFactory;
use Consolidation\AnnotatedCommand\PassThroughArgsInput;
use Consolidation\AnnotatedCommand\Hooks\HookManager;

class Runner
{
    use IO;

    const VERSION = '1.0.0-beta1';
    const ROBOCLASS = 'RoboFile';
    const ROBOFILE = 'RoboFile.php';

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
        if (class_exists($this->roboClass)) {
            return true;
        }

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

    public function execute($argv, $output = null)
    {
        $argv = $this->shebang($argv);
        $input = $this->prepareInput($argv);
        return $this->run($input, $output);
    }

    public function run($input = null, $output = null)
    {
        // If we were not provided a container, then create one
        if (!Config::hasContainer()) {
            // Set up our dependency injection container.
            $container = new RoboContainer();
            static::configureContainer($container, $input, $output);
            static::addServiceProviders($container);
            Config::setContainer($container);

            // Only register a shutdown function when we
            // provide the container.
            register_shutdown_function(array($this, 'shutdown'));
            set_error_handler(array($this, 'handleError'));
        }

        $container = Config::getContainer();
        $output = $container->get('output');
        $app = $container->get('application');

        if (!$this->loadRoboFile()) {
            $this->yell("Robo is not initialized here. Please run `robo init` to create a new RoboFile", 40, 'yellow');
            $app->addInitRoboFileCommand($this->roboFile, $this->roboClass);
            $app->run(Config::input(), Config::output());
            return;
        }

        // Register the RoboFile with the container and then immediately
        // fetch it; this ensures that all of the inflectors will run.
        $commandFileName = "{$this->roboClass}Commands";
        $container->share($commandFileName, $this->roboClass);
        $roboCommandFileInstance = $container->get($commandFileName);

        // Register commands for all of the public methods in the RoboFile.
        $commandFactory = $container->get('commandFactory');
        $commandList = $commandFactory->createCommandsFromClass($roboCommandFileInstance);
        foreach ($commandList as $command) {
            $app->add($command);
        }
        $statusCode = $app->run($input, $output);
        return $statusCode;
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
        $container->add('progressIndicator', \Symfony\Component\Console\Helper\ProgressBar::class)
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
        $container->add('taskBuilder', \Robo\TaskBuilder::class);
        $container->share('application', \Robo\Application::class)
            ->withArgument('Robo')
            ->withArgument(self::VERSION)
            ->withMethodCall('setAutoExit', [false])
            ->withMethodCall('setDispatcher', ['eventDispatcher']);

        static::addInflectors($container);
    }

    public static function addInflectors($container)
    {
        // Register our various inflectors.
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
    public static function addServiceProviders($container)
    {
        $container->addServiceProvider(\Robo\Collection\Collection::getCollectionServices());
        $container->addServiceProvider(\Robo\Task\ApiGen\loadTasks::getApiGenServices());
        $container->addServiceProvider(\Robo\Task\Archive\loadTasks::getArchiveServices());
        $container->addServiceProvider(\Robo\Task\Assets\loadTasks::getAssetsServices());
        $container->addServiceProvider(\Robo\Task\Base\loadTasks::getBaseServices());
        $container->addServiceProvider(\Robo\Task\Npm\loadTasks::getNpmServices());
        $container->addServiceProvider(\Robo\Task\Bower\loadTasks::getBowerServices());
        $container->addServiceProvider(\Robo\Task\Gulp\loadTasks::getGulpServices());
        $container->addServiceProvider(\Robo\Task\Composer\loadTasks::getComposerServices());
        $container->addServiceProvider(\Robo\Task\Development\loadTasks::getDevelopmentServices());
        $container->addServiceProvider(\Robo\Task\Docker\loadTasks::getDockerServices());
        $container->addServiceProvider(\Robo\Task\File\loadTasks::getFileServices());
        $container->addServiceProvider(\Robo\Task\Filesystem\loadTasks::getFilesystemServices());
        $container->addServiceProvider(\Robo\Task\Remote\loadTasks::getRemoteServices());
        $container->addServiceProvider(\Robo\Task\Testing\loadTasks::getTestingServices());
        $container->addServiceProvider(\Robo\Task\Vcs\loadTasks::getVcsServices());
    }

    /**
     * Process a shebang script, if one was used to launch this Runner.
     *
     * @param array $args
     * @return $args with shebang script removed
     */
    protected function shebang($args)
    {
        // Option 1: Shebang line names Robo, but includes no parameters.
        // #!/bin/env robo
        // The robo class may contain multiple commands; the user may
        // select which one to run, or even get a list of commands or
        // run 'help' on any of the available commands as usual.
        if ((count($args) > 1) && $this->isShebangFile($args[1])) {
            return array_merge([$args[0]], array_slice($args, 2));
        }
        // Option 2: Shebang line stipulates which command to run.
        // #!/bin/env robo mycommand
        // The robo class must contain a public method named 'mycommand'.
        // This command will be executed every time.  Arguments and options
        // may be provided on the commandline as usual.
        if ((count($args) > 2) && $this->isShebangFile($args[2])) {
            return array_merge([$args[0]], explode(' ', $args[1]), array_slice($args, 3));
        }
        return $args;
    }

    /**
     * Determine if the specified argument is a path to a shebang script.
     * If so, load it.
     *
     * @param $filepath file to check
     * @return true if shebang script was processed
     */
    protected function isShebangFile($filepath)
    {
        if (!file_exists($filepath)) {
            return false;
        }
        $fp = fopen($filepath, "r");
        if ($fp === false) {
            return false;
        }
        $line = fgets($fp);
        $result = $this->isShebangLine($line);
        if ($result) {
            while ($line = fgets($fp)) {
                $line = trim($line);
                if ($line == '<?php') {
                    $script = stream_get_contents($fp);
                    if (preg_match('#^class *([^ ]+)#m', $script, $matches)) {
                        $this->roboClass = $matches[1];
                        eval($script);
                        $result = true;
                    }
                }
            }
        }
        fclose($fp);

        return $result;
    }

    /**
     * Test to see if the provided line is a robo 'shebang' line.
     */
    protected function isShebangLine($line)
    {
        return ((substr($line, 0, 2) == '#!') && (strstr($line, 'robo') !== false));
    }

    /**
     * @param $argv
     * @return InputInterface
     */
    protected function prepareInput($argv)
    {
        $passThroughArgs = [];
        $pos = array_search('--', $argv);

        // cutting pass-through arguments
        if ($pos !== false) {
            $passThroughArgs = array_slice($argv, $pos+1);
            $argv = array_slice($argv, 0, $pos);
        }

        // loading from other directory
        $pos = array_search('--load-from', $argv) ?: array_search('-f', $argv);
        if ($pos !== false) {
            if (isset($argv[$pos +1])) {
                $this->dir = $argv[$pos +1];
                unset($argv[$pos +1]);
            }
            unset($argv[$pos]);
            // Make adjustments if '--load-from' points at a file.
            if (is_file($this->dir)) {
                $this->roboFile = basename($this->dir);
                $this->dir = dirname($this->dir);
                $className = basename($this->roboFile, '.php');
                if ($className != $this->roboFile) {
                    $this->roboClass = $className;
                }
            }
        }
        $input = new ArgvInput($argv);
        if (!empty($passThroughArgs)) {
            $input = new PassThroughArgsInput($passThroughArgs, $input);
        }
        return $input;
    }

    public function shutdown()
    {
        $error = error_get_last();
        if (!is_array($error)) {
            return;
        }
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
