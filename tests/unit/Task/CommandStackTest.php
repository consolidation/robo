<?php
use Codeception\Util\Stub;

class CommandStackTest extends \Codeception\TestCase\Test
{
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
