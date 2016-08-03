<?php
use Codeception\Util\Stub;
use Robo\Robo;

class CommandStackTest extends \Codeception\TestCase\Test
{
    protected $container;

    protected function _before()
    {
        $this->container = Robo::getContainer();
    }

    public function testExecStackExecutableIsTrimmedFromCommand()
    {
        $commandStack = Stub::make('Robo\Task\CommandStack');
        verify($commandStack
                ->executable('some-executable')
                ->exec('some-executable status')
                ->getCommand()
        )->equals('some-executable status');
    }

    public function testExecStackCommandIsNotTrimmedIfHavingSameCharsAsExecutable()
    {
        $commandStack = Stub::make('Robo\Task\CommandStack');
        verify($commandStack
                ->executable('some-executable')
                ->exec('status')
                ->getCommand()
        )->equals('some-executable status');
    }
}
