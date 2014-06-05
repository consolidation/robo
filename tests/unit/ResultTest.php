<?php
use Robo\Result;

class ResultTest extends \Codeception\TestCase\Test {

    public function testBasics()
    {
        $task = new ResultDummyTask();
        $result = new Result($task, 1, 'The foo barred', ['time' => 0]);

        $this->assertSame($task, $result->getTask());
        $this->assertEquals(1, $result->getExitCode());
        $this->assertEquals('The foo barred', $result->getMessage());
        $this->assertEquals(['time' => 0], $result->getData());

        $taskClone = $result->cloneTask();
        $this->assertNotSame($task, $taskClone);
        $this->assertInstanceOf('Robo\Task\Shared\TaskInterface', $taskClone);
    }
}

class ResultDummyTask implements \Robo\Task\Shared\TaskInterface
{
    public function run()
    {
    }
}
