<?php
namespace Robo;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

class Runner {

    const VERSION = '0.1.0';
    const ROBOCLASS = 'RoboFile';
    const ROBOFILE = 'RoboFile.php';

    protected $currentDir = '.';

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
        $filename = $this->currentDir.DIRECTORY_SEPARATOR.self::ROBOFILE;
        if (!file_exists($filename)) {
            $this->output->writeln("<error> ".self::ROBOFILE." not found in this dir </error>");
            exit;
        }
        require_once $filename;

        if (!class_exists(self::ROBOCLASS)) {
            $this->output->writeln("<error>Class ".self::ROBOCLASS." was not loaded</error>");
        }
    }

    public function execute()
    {
        $app = new Application('Robo', self::VERSION);

        $this->loadRoboFile();
        $className = self::ROBOCLASS;
        $roboTasks = new $className;
        $taskNames = get_class_methods(self::ROBOCLASS);
        $output = $this->output;
        foreach ($taskNames as $taskName) {
            $command = $this->createCommand($taskName);
            $desc = $command->getDescription();
            $command->setCode(function(InputInterface $input) use ($roboTasks, $taskName) {
                $args = $input->getArguments();
                array_shift($args);
                $args[] = $input->getOptions();
                call_user_func_array([$roboTasks, $taskName], $args);

            });
            $app->add($command);
        }

        $app->run();
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
            } else {
                $task->addArgument($name, InputArgument::OPTIONAL, '', $val);
            }
        }

        return $task;
    }
    
    

}
 