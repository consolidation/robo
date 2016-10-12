<?php
use Robo\Result;
use Robo\Exception\TaskExitException;

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
        $data = $result->getData();
        $this->assertEquals(10, $data['time']);

        $taskClone = $result->cloneTask();
        $this->assertNotSame($task, $taskClone);
        $this->assertInstanceOf('Robo\Contract\TaskInterface', $taskClone);
    }

    public function testArrayAccess()
    {
        $task = new ResultDummyTask();
        $result = new Result($task, 1, 'The foo barred', ['time' => 10]);
        $this->assertEquals($result['time'], 10);
    }

    public function testStopOnFail()
    {
        $exceptionClass = false;
        $task = new ResultDummyTask();

        Result::$stopOnFail = true;
        $result = Result::success($task, "Something that worked");
        try {
            $result = Result::error($task, "Something that did not work");
            // stopOnFail will cause Result::error() to throw an exception,
            // so we will never get here. If we did, the assert below would fail.
            $this->assertTrue($result->wasSuccessful());
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $exceptionClass = get_class($e);
        }
        $this->assertEquals(TaskExitException::class, $exceptionClass);
        $this->assertTrue($result->wasSuccessful());

        /*
        // This gives an error:
        //    Exception of class Robo\Exception\TaskExitException expected to
        //    be thrown, but PHPUnit_Framework_Exception caught
        // This happens whether or not the expected exception is thrown
        $this->guy->expectException(TaskExitException::class, function() {
            // $result = Result::error($task, "Something that did not work");
            $result = Result::success($task, "Something that worked");
        });
        */

        Result::$stopOnFail = false;
    }
}

class ResultDummyTask implements \Robo\Contract\TaskInterface
{
    public function run()
    {
    }
}
