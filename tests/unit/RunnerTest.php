<?php
class RunnerTest extends \Codeception\TestCase\Test
{
    /**
     * @var \Robo\Runner
     */
    private $runner;

    public function _before()
    {
        $this->runner = new \Robo\Runner();
    }



    public function testHandleError()
    {
        $tmpLevel = error_reporting();

        $this->assertFalse($this->runner->handleError());
        error_reporting(0);
        $this->assertTrue($this->runner->handleError());

        error_reporting($tmpLevel);
    }

    public function testErrorIsHandled()
    {
        $tmpLevel = error_reporting();

        error_reporting(E_USER_ERROR);
        set_error_handler(array($this->runner, 'handleError'));
        @trigger_error('test error', E_USER_ERROR);
        $this->assertEmpty(error_get_last());

        error_reporting(0);
        trigger_error('test error', E_USER_ERROR);
        $this->assertEmpty(error_get_last());

        error_reporting($tmpLevel);
    }

}