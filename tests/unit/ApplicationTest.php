<?php
require_once codecept_data_dir() . 'TestedRoboFile.php';

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

    const ROBOFILE = 'TestedRoboFile';

    protected function _before()
    {
        $this->app = new \Robo\Application('Robo', \Robo\Runner::VERSION);
        $this->app->addCommandsFromClass(self::ROBOFILE);
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
        return $this->app->createCommand(new \Robo\TaskInfo(self::ROBOFILE, $name));
    }
}
