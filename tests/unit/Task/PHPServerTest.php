<?php

use AspectMock\Test as test;

class PHPServerTest extends \Codeception\TestCase\Test
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
        test::double('Robo\Task\Development\PhpServer', ['output' => new \Symfony\Component\Console\Output\NullOutput()]);
    }

    public function testServerBackgroundRun()
    {
        $task = new \Robo\Task\Development\PhpServer('8000');
        $task->setLogger(new \Psr\Log\NullLogger());

        $task->background()->run();
        $this->process->verifyInvoked('start');
    }

    public function testServerRun()
    {
        $task = new \Robo\Task\Development\PhpServer('8000');
        $task->setLogger(new \Psr\Log\NullLogger());

        $task->run();
        $this->process->verifyInvoked('run');
    }

    public function testServerCommand()
    {
        if (strtolower(PHP_OS) === 'linux') {
            $expectedCommand = 'exec php -S 127.0.0.1:8000 -t web';
        } else {
            $expectedCommand = 'php -S 127.0.0.1:8000 -t web';
        }

        verify(
            (new \Robo\Task\Development\PhpServer('8000'))
                ->host('127.0.0.1')
                ->dir('web')
                ->getCommand()
        )->equals($expectedCommand);
    }

}
