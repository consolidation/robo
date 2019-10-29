<?php
use Codeception\Util\Stub;

class CommandStackTest extends \Codeception\TestCase\Test
{
    public function testExecStackExecutableIsTrimmedFromCommand()
    {
        $commandStack = Stub::make('Robo\Task\CommandStack');
        $this->assertEquals(
            'some-executable status',
            $commandStack
                ->executable('some-executable')
                ->exec('some-executable status')
                ->getCommand()
        );
    }

    public function testExecStackCommandIsNotTrimmedIfHavingSameCharsAsExecutable()
    {
        $commandStack = Stub::make('Robo\Task\CommandStack');
        $this->assertEquals(
            'some-executable status',
            $commandStack
                ->executable('some-executable')
                ->exec('status')
                ->getCommand()
        );
    }
}
