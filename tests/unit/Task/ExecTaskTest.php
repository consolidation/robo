<?php
use AspectMock\Test as test;
use Robo\Robo;

class ExecTaskTest extends \Codeception\TestCase\Test
{
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
        $result = (new \Robo\Task\Base\Exec('ls'))->run();
        $this->process->verifyInvoked('run');
        verify($result->getMessage())->equals('Hello world');
        verify($result->getExitCode())->equals(0);
    }

    public function testExecInBackground()
    {
        $result = (new \Robo\Task\Base\Exec('ls'))->background()->run();
        $this->process->verifyInvoked('start');
        $this->process->verifyNeverInvoked('run');
        verify('exit code was not received', $result->getExitCode())->notEquals(100);
    }

    public function testGetCommand()
    {
        verify((new \Robo\Task\Base\Exec('ls'))->getCommand())->equals('ls');
    }

    public function testExecStack()
    {
        (new \Robo\Task\Base\ExecStack())
            ->exec('ls')
            ->exec('cd /')
            ->exec('cd home')
            ->run();
        $this->process->verifyInvoked('run', 3);
    }

    public function testExecStackCommand()
    {
        verify((new \Robo\Task\Base\ExecStack())
            ->exec('ls')
            ->exec('cd /')
            ->exec('cd home')
            ->getCommand()
        )->equals('ls && cd / && cd home');
    }
};
