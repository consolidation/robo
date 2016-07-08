<?php
require_once codecept_data_dir() . 'TestedRoboFile.php';

use Robo\Runner;
use Robo\Container\RoboContainer;
use Consolidation\AnnotatedCommand\AnnotatedCommandFactory;
use Consolidation\AnnotatedCommand\Parser\CommandInfo;

class ApplicationTest extends \Codeception\TestCase\Test
{
    /**
     * @var \Robo\Runner
     */
    private $runner;

    /**
     * @var \Robo\Application
     */
    private $app;

    /**
     * @var Consolidation\AnnotatedCommand\AnnotatedCommandFactory
     */
    private $commandFactory;

    /**
     * @var TestRoboFile
     */
    private $roboCommandFileInstance;

    protected function _before()
    {
        $container = new RoboContainer();
        \Robo\Runner::configureContainer($container);
        \Robo\Runner::addServiceProviders($container);
        \Robo\Config::setContainer($container);
        $this->app = $container->get('application');
        $this->commandFactory = $container->get('commandFactory');
        $this->roboCommandFileInstance = new TestedRoboFile;
        $this->roboCommandFileInstance->setContainer(\Robo\Config::getContainer());
        $commandList = $this->commandFactory->createCommandsFromClass($this->roboCommandFileInstance);
        foreach ($commandList as $command) {
            $this->app->add($command);
        }
    }

    public function testTaskAccessor()
    {
        // Get a reference to the protected 'task' method, as
        // this is normally only callable by methods of the
        // commandfile instance.
        $method = new ReflectionMethod($this->roboCommandFileInstance, 'task');
        $method->setAccessible(true);
        $builder = $method->invoke($this->roboCommandFileInstance, 'taskExec', ['ls']);
        verify(get_class($builder))->equals('Robo\TaskBuilder');
        $task = $builder->getTaskBuilderCurrentTask();
        verify(get_class($task))->equals('Robo\Task\Base\Exec');
        // If 'task' is not provided, then it will be supplied (that is,
        // the task's classname may also be used with the 'task()' method).
        $builder = $method->invoke($this->roboCommandFileInstance, 'Exec', ['ls']);
        verify(get_class($builder))->equals('Robo\TaskBuilder');
        $task = $builder->getTaskBuilderCurrentTask();
        verify(get_class($task))->equals('Robo\Task\Base\Exec');
    }

    public function testAllowEmptyValuesAsDefaultsToOptionalOptions()
    {
        $command = $this->createCommand('hello');

        $yell = $command->getDefinition()->getOption('yell');

        verify($yell->isValueOptional())
            ->equals(false);
        verify($yell->getDefault())
            ->equals(false);

        $to = $command->getDefinition()->getOption('to');

        verify($to->isValueOptional())
            ->equals(true);
        verify($to->getDefault())
            ->equals(null);
    }

    public function testCommandDocumentation()
    {
        $command = $this->createCommand('fibonacci');

        verify($command->getDescription())
            ->equals('Calculate the fibonacci sequence between two numbers.');
    }

    public function testCommandCompactDocumentation()
    {
        $command = $this->createCommand('compact');

        verify($command->getDescription())
            ->equals('Compact doc comment');
    }

    public function testCommandArgumentDocumentation()
    {
        $command = $this->createCommand('fibonacci');

        $start = $command->getDefinition()->getArgument('start');

        verify($start->getDescription())
            ->equals('Number to start from');

        $steps = $command->getDefinition()->getArgument('steps');

        verify($steps->getDescription())
            ->equals('Number of steps to perform');
    }

    public function testCommandOptionDocumentation()
    {
        $command = $this->createCommand('fibonacci');

        $graphic = $command->getDefinition()->getOption('graphic');

        verify($graphic->getDescription())
            ->equals('Display the sequence graphically using cube representation');
    }

    public function testCommandHelpDocumentation()
    {
        $command = $this->createCommand('fibonacci');

        verify($command->getHelp())
            ->contains('    +----+---+');
    }

    public function testCommandNaming()
    {
        $this->assertNotNull($this->app->find('generate:user-avatar'));
    }

    protected function createCommand($name)
    {
        $commandInfo = new CommandInfo($this->roboCommandFileInstance, $name);
        return $this->commandFactory->createCommand($commandInfo, $this->roboCommandFileInstance);
    }
}
