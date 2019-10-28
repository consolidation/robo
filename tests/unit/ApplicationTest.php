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
        $this->assertEquals(
            'Robo\Collection\CollectionBuilder',
            get_class($collectionBuilder));
        $task = $collectionBuilder->getCollectionBuilderCurrentTask();
        $this->assertEquals(
            'Robo\Task\Base\Exec',
            get_class($task));
        $this->assertEquals(
            'Robo\Task\Base\Exec',
            get_class($task));
    }

    public function testAllowEmptyValuesAsDefaultsToOptionalOptions()
    {
        $command = $this->createCommand('hello');

        $yell = $command->getDefinition()->getOption('yell');

        $this->assertFalse($yell->isValueOptional());
        $this->assertFalse($yell->getDefault());

        $to = $command->getDefinition()->getOption('to');

        $this->assertTrue($to->isValueOptional());
        $this->assertNull($to->getDefault());
    }

    public function testCommandDocumentation()
    {
        $command = $this->createCommand('fibonacci');

        $this->assertEquals(
            'Calculate the fibonacci sequence between two numbers.',
            $command->getDescription());
    }

    public function testCommandCompactDocumentation()
    {
        $command = $this->createCommand('compact');

        $this->assertEquals(
            'Compact doc comment',
            $command->getDescription());
    }

    public function testCommandArgumentDocumentation()
    {
        $command = $this->createCommand('fibonacci');

        $start = $command->getDefinition()->getArgument('start');

        $this->assertEquals(
            'Number to start from',
            $start->getDescription());

        $steps = $command->getDefinition()->getArgument('steps');

        $this->assertEquals(
            'Number of steps to perform',
            $steps->getDescription());
    }

    public function testCommandOptionDocumentation()
    {
        $command = $this->createCommand('fibonacci');

        $graphic = $command->getDefinition()->getOption('graphic');

        $this->assertEquals(
            'Display the sequence graphically using cube representation',
            $graphic->getDescription());
    }

    public function testCommandHelpDocumentation()
    {
        $command = $this->createCommand('fibonacci');

        $this->assertContains(
            '+----+---+',
            $command->getHelp());
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
