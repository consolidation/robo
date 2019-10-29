<?php
use AspectMock\Test as test;

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
            'getExitCode' => 0,
            'logger' => new \Psr\Log\NullLogger(),
        ]);
        test::double('Robo\Task\Base\Exec', ['output' => new \Symfony\Component\Console\Output\NullOutput()]);
    }

    public function testExec()
    {
        $task = new \Robo\Task\Base\Exec('ls');
        $task->setLogger(new \Psr\Log\NullLogger());

        $result = $task->run();
        $this->process->verifyInvoked('run');
        $this->assertEquals(
            'Hello world',
            $result->getMessage());
        $this->assertEquals(0, $result->getExitCode());
    }

    public function testExecInBackground()
    {
        $task = new \Robo\Task\Base\Exec('ls');
        $task->setLogger(new \Psr\Log\NullLogger());

        $result = $task->background()->run();
        $this->process->verifyInvoked('start');
        $this->process->verifyNeverInvoked('run');
        $this->assertNotEquals(100, $result->getExitCode());
    }

    public function testGetCommand()
    {
        $this->assertEquals(
            'ls',
            (new \Robo\Task\Base\Exec('ls'))->getCommand());
    }

    public function testExecStack()
    {
        $task = new \Robo\Task\Base\ExecStack();
        $task->setLogger(new \Psr\Log\NullLogger());

        $task
            ->exec('ls')
            ->exec('cd /')
            ->exec('cd home')
            ->run();
        $this->process->verifyInvoked('run', 3);
    }

    public function testExecStackCommand()
    {
        $this->assertEquals(
            'ls && cd / && cd home',
            (new \Robo\Task\Base\ExecStack())
            ->exec('ls')
            ->exec('cd /')
            ->exec('cd home')
            ->getCommand()
        );
    }

    public function testExecStackCommandInterface()
    {
        $this->assertEquals(
            'ls && git add -A && git pull',
            (new \Robo\Task\Base\ExecStack())
            ->exec('ls')
            ->exec((new \Robo\Task\Vcs\GitStack('git'))->add('-A')->pull())
            ->getCommand()
        );
    }
};
