<?php
use AspectMock\Test as test;

class ExecTaskTest extends \Codeception\TestCase\Test
{
    use \Robo\Task\Base\loadTasks;
    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $process;

    protected function _before()
    {
        $this->process = test::double('Symfony\Component\Process\Process', [
            'run' => false,
            'start' => false,
            'getOutput' => 'Hello world',
            'getExitCode' => 0
        ]);
        test::double('Robo\Task\Base\Exec', ['getOutput' => new \Symfony\Component\Console\Output\NullOutput()]);
    }

    public function testExec()
    {
        $result = $this->taskExec('ls')->run();
        $this->process->verifyInvoked('run');
        verify($result->getMessage())->equals('Hello world');
        verify($result->getExitCode())->equals(0);
    }

    public function testExecInBackground()
    {
        $result = $this->taskExec('ls')->background()->run();
        $this->process->verifyInvoked('start');
        $this->process->verifyNeverInvoked('run');
        verify('exit code was not received', $result->getExitCode())->notEquals(100);
    }

    public function testGetCommand()
    {
        verify($this->taskExec('ls')->getCommand())->equals('ls');
    }

    public function testExecStack()
    {
        $this->taskExecStack()
            ->exec('ls')
            ->exec('cd /')
            ->exec('cd home')
            ->run();
        $this->process->verifyInvoked('run', 3);
    }

    public function testExecStackCommand()
    {
        verify($this->taskExecStack()
            ->exec('ls')
            ->exec('cd /')
            ->exec('cd home')
            ->getCommand()
        )->equals('ls && cd / && cd home');
    }
};