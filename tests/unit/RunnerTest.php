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
        /** @var \Symfony\Component\Console\Command\Command $command */
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

    protected function _before()
    {
        parent::_before();
        require_once codecept_data_dir() . DIRECTORY_SEPARATOR . \Robo\Runner::ROBOFILE;

        $this->reflector = new ReflectionClass('Robo\\Runner');
        $this->runner = new \Robo\Runner;
    }

    protected function callMethod($name, $args = array())
    {
        $method = $this->reflector->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($this->runner, $args);
    }
}
