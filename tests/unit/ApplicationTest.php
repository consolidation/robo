<?php
require_once codecept_data_dir() . 'TestedRoboFile.php';

use Robo\Robo;
use Consolidation\AnnotatedCommand\Parser\CommandInfo;
use Robo\Collection\CollectionBuilder;

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
        $container = Robo::createDefaultContainer();

        $this->app = $container->get('application');
        $config = $container->get('config');
        $this->commandFactory = $container->get('commandFactory');
        $this->roboCommandFileInstance = new TestedRoboFile;
        $builder = CollectionBuilder::create($container, $this->roboCommandFileInstance);
        $this->roboCommandFileInstance->setBuilder($builder);
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
        $collectionBuilder = $method->invoke($this->roboCommandFileInstance, 'Robo\Task\Base\Exec', 'ls');
        verify(get_class($collectionBuilder))->equals('Robo\Collection\CollectionBuilder');
        $task = $collectionBuilder->getCollectionBuilderCurrentTask();
        verify(get_class($task))->equals('Robo\Task\Base\Exec');
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
            ->contains('+----+---+');
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
