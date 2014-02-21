<?php
namespace Robo\Tests;

use PHPUnit_Framework_TestCase;
use Mockery as m;

use Robo\Result;

class ResultTest extends PHPUnit_Framework_TestCase {

    public function testBasics()
    {
        $task = m::mock('Robo\Task\TaskInterface');
        $result = new Result($task, 1, 'The foo barred', ['time' => 0]);

        $this->assertSame($task, $result->getTask());
        $this->assertEquals(1, $result->getExitCode());
        $this->assertEquals('The foo barred', $result->getMessage());
        $this->assertEquals(['time' => 0], $result->getData());

        $taskClone = $result->cloneTask();
        $this->assertNotSame($task, $taskClone);
        $this->assertInstanceOf('Robo\Task\TaskInterface', $taskClone);
    }
}
