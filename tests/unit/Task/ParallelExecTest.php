<?php
use AspectMock\Test as test;

class ParallelExecTest extends \Codeception\TestCase\Test
{
    /**
     * @var \CodeGuy
     */
    protected $guy;

    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $process;

    protected function _before()
    {
        $this->process = test::double('Symfony\Component\Process\Process', [
            'run' => false,
            'start' => false,
            'isRunning' => false,
            'getOutput' => 'Hello world',
            'getExitCode' => 0,
            'logger' => new \Psr\Log\NullLogger(),
        ]);
    }

    public function testParallelExec()
    {
        $task = new \Robo\Task\Base\ParallelExec();
        $task->setLogger($this->guy->logger());

        $result = $task
            ->process('ls 1')
            ->process('ls 2')
            ->process('ls 3')
            ->run();
        $this->process->verifyInvokedMultipleTimes('start', 3);
        verify($result->getExitCode())->equals(0);
        $this->guy->seeInOutput("3 processes finished");
    }

    public function testParallelExecWithWaitInterval()
    {
        $task = new \Robo\Task\Base\ParallelExec();
        $task->setLogger($this->guy->logger());

        $result = $task
            ->process('ls 1')
            ->process('ls 2')
            ->process('ls 3')
            ->waitInterval(1)
            ->run();
        $this->process->verifyInvokedMultipleTimes('start', 3);
        verify($result->getExitCode())->equals(0);
        $this->guy->seeInOutput("3 processes finished");
    }
}
