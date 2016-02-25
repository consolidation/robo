<?php
namespace Robo;

use Robo\Common\IO;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class Runner
{
    use IO;

    const VERSION = '0.7.1';
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
    public function __construct($roboClass = null, $roboFile = null)
    {
        // set the const as class properties to allow overwriting in child classes
        $this->roboClass = $roboClass ? $roboClass : self::ROBOCLASS ;
        $this->roboFile  = $roboFile ? $roboFile : self::ROBOFILE;
        $this->dir = getcwd();
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
        Config::setOutput(new ConsoleOutput());

        $input = $this->prepareInput($input ? $input : $_SERVER['argv']);
        Config::setInput($input);
        $app = new Application('Robo', self::VERSION);

        if (!$this->loadRoboFile()) {
            $this->yell("Robo is not initialized here. Please run `robo init` to create a new RoboFile", 40, 'yellow');
            $app->addInitRoboFileCommand($this->roboFile, $this->roboClass);
            $app->run($input);
            return;
        }
        $app->addCommandsFromClass($this->roboClass, $this->passThroughArgs);
        $app->setAutoExit(false);
        return $app->run($input);
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

