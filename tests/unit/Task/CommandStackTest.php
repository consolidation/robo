<?php
use Codeception\Util\Stub;
use Robo\Runner;
use Robo\Container\RoboContainer;

class CommandStackTest extends \Codeception\TestCase\Test
{
    protected $container;

    protected function _before()
    {
        $this->container = new RoboContainer();
        Runner::configureContainer($this->container);
    }

    public function testExecStackExecutableIsTrimmedFromCommand()
    {
        $commandStack = Stub::make('Robo\Task\CommandStack', array(
            'executable' => 'some-executable'
        ));
        verify($commandStack
                ->exec('some-executable status')
                ->getCommand()
        )->equals('some-executable status');
    }

    public function testExecStackCommandIsNotTrimmedIfHavingSameCharsAsExecutable()
    {
        $commandStack = Stub::make('Robo\Task\CommandStack', array(
            'executable' => 'some-executable'
        ));
        verify($commandStack
                ->exec('status')
                ->getCommand()
        )->equals('some-executable status');
    }
}
