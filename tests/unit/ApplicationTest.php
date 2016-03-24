<?php
require_once codecept_data_dir() . 'TestedRoboFile.php';

use Consolidation\AnnotationCommand\AnnotationCommandFactory;
use Consolidation\AnnotationCommand\CommandInfo;

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
     * @var Consolidation\AnnotationCommand\AnnotationCommandFactory
     */
    private $commandFactory;

    /**
     * @var TestRoboFile
     */
    private $roboCommandFileInstance;

    protected function _before()
    {
        $this->app = new \Robo\Application('Robo', \Robo\Runner::VERSION);
        $this->commandFactory = new AnnotationCommandFactory();
        $this->roboCommandFileInstance = new TestedRoboFile;
        $commandList = $this->commandFactory->createCommandsFromClass($this->roboCommandFileInstance);
        foreach ($commandList as $command) {
            $this->app->add($command);
        }
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
