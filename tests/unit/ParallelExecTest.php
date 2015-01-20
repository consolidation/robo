<?php
use AspectMock\Test as test;

class ParallelExecTest extends \Codeception\TestCase\Test
{
    use \Robo\Task\Base\loadTasks;
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
            'getExitCode' => 0
        ]);
    }

    public function testParallelExec()
    {
        $result = $this->taskParallelExec()
            ->process('ls 1')
            ->process('ls 2')
            ->process('ls 3')
            ->run();
        $this->process->verifyInvokedMultipleTimes('start', 3);
        verify($result->getExitCode())->equals(0);
        $this->guy->seeInOutput("3 processes finished");
    }

}