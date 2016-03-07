<?php
namespace Robo;

use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class Application extends  SymfonyApplication
{
    /**
     * @var \Robo\TaskAssembler
     */
    protected $taskAssembler;

    public function __construct($name, $version)
    {
        parent::__construct($name, $version);

        $this->getDefinition()->addOption(
            new InputOption('--simulate', null, InputOption::VALUE_NONE, 'Run in simulated mode (show what would have happened).')
        );
    }

    public function setTaskAssembler($taskAssembler)
    {
        $this->taskAssembler = $taskAssembler;
    }

    public function taskAssembler()
    {
        return $this->taskAssembler;
    }

    public function addCommandsFromClass($className, $passThrough = null)
    {
        $roboTasks = new $className;
        if ($roboTasks instanceof \Robo\Tasks) {
            $roboTasks->setTaskAssembler($this->taskAssembler);
        }

        $commandNames = array_filter(get_class_methods($className), function($m) {
            return !in_array($m, ['__construct']);
        });

        foreach ($commandNames as $commandName) {
            $command = $this->createCommand(new TaskInfo($className, $commandName));
            $command->setCode(function(InputInterface $input) use ($roboTasks, $commandName, $passThrough) {
                // get passthru args
                $args = $input->getArguments();
                array_shift($args);
                if ($passThrough) {
                    $args[key(array_slice($args, -1, 1, TRUE))] = $passThrough;
                }
                $args[] = $input->getOptions();
                // Need a better way to handle global options
                // Also, this is not necessarily the best place to do this
                Config::setGlobalOptions($input);
                // Avoid making taskAssembler depend on Config class.
                Config::service('taskAssembler')->setSimulated(Config::isSimulated());

                $res = call_user_func_array([$roboTasks, $commandName], $args);
                if (is_int($res)) exit($res);
                if (is_bool($res)) exit($res ? 0 : 1);
                if ($res instanceof Result) exit($res->getExitCode());
            });
            $this->add($command);
        }
    }

    public function createCommand(TaskInfo $taskInfo)
    {
        $task = new Command($taskInfo->getName());
        $task->setDescription($taskInfo->getDescription());
        $task->setHelp($taskInfo->getHelp());

        $args = $taskInfo->getArguments();
        foreach ($args as $name => $val) {
            $description = $taskInfo->getArgumentDescription($name);
            if ($val === TaskInfo::PARAM_IS_REQUIRED) {
                $task->addArgument($name, InputArgument::REQUIRED, $description);
            } elseif (is_array($val)) {
                $task->addArgument($name, InputArgument::IS_ARRAY, $description, $val);
            } else {
                $task->addArgument($name, InputArgument::OPTIONAL, $description, $val);
            }
        }
        $opts = $taskInfo->getOptions();
        foreach ($opts as $name => $val) {
            $description = $taskInfo->getOptionDescription($name);

            $fullName = $name;
            $shortcut = '';
            if (strpos($name, '|')) {
                list($fullName, $shortcut) = explode('|', $name, 2);
            }

            if (is_bool($val)) {
                $task->addOption($fullName, $shortcut, InputOption::VALUE_NONE, $description);
            } else {
                $task->addOption($fullName, $shortcut, InputOption::VALUE_OPTIONAL, $description, $val);
            }
        }

        return $task;
    }

    public function addInitRoboFileCommand($roboFile, $roboClass)
    {
        $createRoboFile = new Command('init');
        $createRoboFile->setDescription("Intitalizes basic RoboFile in current dir");
        $createRoboFile->setCode(function() use ($roboClass, $roboFile) {
            $output = Config::get('output');
            $output->writeln("<comment>  ~~~ Welcome to Robo! ~~~~ </comment>");
            $output->writeln("<comment>  ". $roboFile ." will be created in current dir </comment>");
            file_put_contents(
                $roboFile,
                '<?php'
                . "\n/**"
                . "\n * This is project's console commands configuration for Robo task runner."
                . "\n *"
                . "\n * @see http://robo.li/"
                . "\n */"
                . "\nclass " . $roboClass . " extends \\Robo\\Tasks\n{\n    // define public methods as commands\n}"
            );
            $output->writeln("<comment>  Edit RoboFile.php to add your commands! </comment>");
        });
        $this->add($createRoboFile);
    }
}
