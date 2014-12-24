<?php

class RunnerTest extends \Codeception\TestCase\Test
{
    /**
     * @var \ReflectionClass
     */
    private $reflector;

    /**
     * @var \Robo\Runner
     */
    private $runner;

    public function testAllowEmptyValuesAsDefaultsToOptionalOptions()
    {
        $command = $this->callMethod('createCommand', ['hello']);

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
        $command = $this->callMethod('createCommand', ['fibonacci']);

        verify($command->getDescription())
            ->equals('Calculate the fibonacci sequence between two numbers.');
    }

    public function testCommandCompactDocumentation()
    {
        $command = $this->callMethod('createCommand', ['compact']);

        verify($command->getDescription())
            ->equals('Compact doc comment');
    }

    public function testCommandArgumentDocumentation()
    {
        $command = $this->callMethod('createCommand', ['fibonacci']);

        $start = $command->getDefinition()->getArgument('start');

        verify($start->getDescription())
            ->equals('Number to start from');

        $steps = $command->getDefinition()->getArgument('steps');

        verify($steps->getDescription())
            ->equals('Number of steps to perform');
    }

    public function testCommandOptionDocumentation()
    {
        $command = $this->callMethod('createCommand', ['fibonacci']);

        $graphic = $command->getDefinition()->getOption('graphic');

        verify($graphic->getDescription())
            ->equals('Display the sequence graphically using cube representation');
    }

    public function testCommandHelpDocumentation()
    {
        $command = $this->callMethod('createCommand', ['fibonacci']);

        verify($command->getHelp())
            ->contains('    +----+---+');
    }

    protected function _before()
    {
        parent::_before();
        require_once codecept_data_dir() . DIRECTORY_SEPARATOR . \Robo\Runner::ROBOFILE;

        $this->reflector = new ReflectionClass('Robo\\Runner');
        $this->runner = new \Robo\Runner;
    }

    /**
     * @param string $name
     * @param array $args
     * @return \Symfony\Component\Console\Command\Command
     */
    protected function callMethod($name, $args = array())
    {
        $method = $this->reflector->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($this->runner, $args);
    }
}
