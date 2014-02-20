<?php
namespace Robo;

use Robo\Command\Init;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

class Runner {

    const VERSION = '0.3.2';
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
        if (!file_exists(self::ROBOFILE)) {
            $this->output->writeln("<comment>  ".self::ROBOFILE." not found in this dir </comment>");
            $dialog = new DialogHelper();
            if ($dialog->askConfirmation($this->output, "<question>  Should I create RoboFile here? (y/n)  \n</question>", false)) {
                $this->initRoboFile();
            }
            exit;
        }
        require_once self::ROBOFILE;

        if (!class_exists(self::ROBOCLASS)) {
            $this->output->writeln("<error>Class ".self::ROBOCLASS." was not loaded</error>");
            return false;
        }
        return true;
    }

    public function execute()
    {
        $app = new Application('Robo', self::VERSION);

        $loaded = $this->loadRoboFile();
        if (!$loaded) {
            $app->add(new Init('init'));
            $app->run();
            return;
        }

        $className = self::ROBOCLASS;
        $roboTasks = new $className;
        $taskNames = get_class_methods(self::ROBOCLASS);
        foreach ($taskNames as $taskName) {
            $command = $this->createCommand($taskName);
            $command->setCode(function(InputInterface $input) use ($roboTasks, $taskName) {
                $args = $input->getArguments();
                array_shift($args);
                $args[] = $input->getOptions();
                $res = call_user_func_array([$roboTasks, $taskName], $args);
                if (is_int($res)) exit($res);
                if (is_bool($res)) exit($res ? 0 : 1);
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

    protected function initRoboFile()
    {
        file_put_contents(self::ROBOFILE, "<?php\nclass Robofile\n{\n    use Robo\\Output;\n    // define public methods as commands\n}");
        $this->output->writeln(self::ROBOFILE . " created");

    }
    
    

}
 