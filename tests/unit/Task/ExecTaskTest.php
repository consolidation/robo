<?php
use AspectMock\Test as test;
use Robo\Runner;
use Robo\Container\RoboContainer;

class ExecTaskTest extends \Codeception\TestCase\Test
{
    protected $container;

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
        $this->container = new RoboContainer();
        Runner::configureContainer($this->container);
        $this->container->addServiceProvider(\Robo\Task\Base\ServiceProvider::class);
    }

    public function testExec()
    {
        $result = $this->container->get('taskExec', ['ls'])->run();
        $this->process->verifyInvoked('run');
        verify($result->getMessage())->equals('Hello world');
        verify($result->getExitCode())->equals(0);
    }

    public function testExecInBackground()
    {
        $result = $this->container->get('taskExec', ['ls'])->background()->run();
        $this->process->verifyInvoked('start');
        $this->process->verifyNeverInvoked('run');
        verify('exit code was not received', $result->getExitCode())->notEquals(100);
    }

    public function testGetCommand()
    {
        verify($this->container->get('taskExec', ['ls'])->getCommand())->equals('ls');
    }

    public function testExecStack()
    {
        $this->container->get('taskExecStack')
            ->exec('ls')
            ->exec('cd /')
            ->exec('cd home')
            ->run();
        $this->process->verifyInvoked('run', 3);
    }

    public function testExecStackCommand()
    {
        verify($this->container->get('taskExecStack')
            ->exec('ls')
            ->exec('cd /')
            ->exec('cd home')
            ->getCommand()
        )->equals('ls && cd / && cd home');
    }
};
