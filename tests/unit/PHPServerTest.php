<?php

use AspectMock\Test as test;
class PHPServerTest extends \Codeception\TestCase\Test
{
    use \Robo\Task\Development\loadTasks;
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
        test::double('Robo\Task\Development\PhpServer', ['getOutput' => new \Symfony\Component\Console\Output\NullOutput()]);
    }

    public function testServerBackgroundRun()
    {
        $this->taskServer('8000')->background()->run();
        $this->process->verifyInvoked('start');
    }

    public function testServerRun()
    {
        $this->taskServer('8000')->run();
        $this->process->verifyInvoked('run');
    }

    public function testServerCommand()
    {
        verify(
            $this->taskServer('8000')
                ->dir('web')
                ->getCommand()
        )->equals('php -S 127.0.0.1:8000 -t web');
    }

}