<?php
namespace Robo;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;

class Runner {

    const VERSION = '0.4.3';
    const ROBOCLASS = 'Robofile';
    const ROBOFILE = 'robofile.php';

    protected $currentDir = '.';
    protected $passThroughArgs = null;

    /**
     * @var ConsoleOutput
     */
    protected $output;

    public function __construct()
    {
        $this->output = new ConsoleOutput();
    }

    protected function loadRoboFile()
    {
        if (!file_exists(self::ROBOFILE)) {
            $this->output->writeln("<comment>  " . self::ROBOFILE . " not found in this dir </comment>");
            $dialog = new DialogHelper();
            if ($dialog->askConfirmation($this->output, "<question>  Should I create " . self::ROBOFILE . " here? (y/n)  \n</question>", false)) {
                $this->initRoboFile();
            }
            exit;
        }
        require_once self::ROBOFILE;

        if (!class_exists(self::ROBOCLASS)) {
            $this->output->writeln("<error>Class " . self::ROBOCLASS . " was not loaded</error>");
            return false;
        }
        return true;
    }

    public function execute()
    {
        register_shutdown_function(array($this, 'shutdown'));
        $app = new Application('Robo', self::VERSION);

        $loaded = $this->loadRoboFile();
        if (!$loaded) {
            $app->add(new Init('init'));
            $app->run();
            return;
        }
        $input = $this->prepareInput();

        $className = self::ROBOCLASS;
        $roboTasks = new $className;
        $taskNames = get_class_methods(self::ROBOCLASS);
        $passThrough = $this->passThroughArgs;
        foreach ($taskNames as $taskName) {
            $command = $this->createCommand($taskName);
            $command->setCode(function(InputInterface $input) use ($roboTasks, $taskName, $passThrough) {
                // get passthru args
                $args = $input->getArguments();
                array_shift($args);
                if ($passThrough) {
                    $args[key(array_slice($args, -1, 1, TRUE))] = $passThrough;
                }
                $args[] = $input->getOptions();
                $res = call_user_func_array([$roboTasks, $taskName], $args);
                if (is_int($res)) exit($res);
                if (is_bool($res)) exit($res ? 0 : 1);
                if ($res instanceof Result) exit($res->getExitCode());
            });
            $app->add($command);
        }
        $app->run($input);
    }

    protected function prepareInput()
    {
        $argv = $_SERVER['argv'];

        $pos = array_search('--', $argv);
        if ($pos !== false) {
            $this->passThroughArgs = implode(' ', array_slice($argv, $pos+1));
            $argv = array_slice($argv, 0, $pos);
        }
        return new ArgvInput($argv);
    }

    protected function createCommand($taskName)
    {
        $taskInfo = new TaskInfo(self::ROBOCLASS, $taskName);
        $task = new Command($taskInfo->getName());
        $task->setDescription($taskInfo->getDescription());

        $args = $taskInfo->getArguments();
        foreach ($args as $name => $val) {
            if ($val === TaskInfo::PARAM_IS_REQUIRED) {
                $task->addArgument($name, InputArgument::REQUIRED);
            } elseif (is_array($val)) {
                $task->addArgument($name, InputArgument::IS_ARRAY, '', $val);
            } else {
                $task->addArgument($name, InputArgument::OPTIONAL, '', $val);
            }
        }
        $opts = $taskInfo->getOptions();
        foreach ($opts as $name => $val) {
            $task->addOption($name, '', InputOption::VALUE_OPTIONAL, '', $val);
        }

        return $task;
    }

    protected function initRoboFile()
    {
        file_put_contents(self::ROBOFILE, "<?php\nclass " . self::ROBOCLASS . " extends \\Robo\\Tasks\n{\n    // define public methods as commands\n}");
        $this->output->writeln(self::ROBOFILE . " created");

    }

    public  function shutdown()
    {
        $error = error_get_last();
        if (!is_array($error)) return;
        $this->output->writeln(sprintf("<error>ERROR: %s \nin %s:%d\n</error>", $error['message'], $error['file'], $error['line']));
    }
}

