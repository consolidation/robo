<?php
use Robo\Result;

class ResultTest extends \Codeception\TestCase\Test {

    /**
     * @var \CodeGuy
     */
    protected $guy;

    public function testBasics()
    {
        $task = new ResultDummyTask();
        $result = new Result($task, 1, 'The foo barred', ['time' => 10]);
        
        $this->guy->seeInOutput('The foo barred');
        $this->guy->seeInOutput('Exit code 1');
        $this->guy->seeInOutput('10s');
        $this->guy->seeInOutput('[ResultDummyTask]');

        $this->assertSame($task, $result->getTask());
        $this->assertEquals(1, $result->getExitCode());
        $this->assertEquals('The foo barred', $result->getMessage());
        $this->assertEquals(['time' => 10], $result->getData());

        $taskClone = $result->cloneTask();
        $this->assertNotSame($task, $taskClone);
        $this->assertInstanceOf('Robo\Contract\TaskInterface', $taskClone);
    }
}

class ResultDummyTask implements \Robo\Contract\TaskInterface
{
    public function run()
    {
    }
}
