<?php
use AspectMock\Test as test;

class PHPUnitTest extends \Codeception\TestCase\Test
{
    use \Robo\Task\Testing\loadTasks;
    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $phpunit;

    protected function _before()
    {
        $this->phpunit = test::double('Robo\Task\Testing\PHPUnit', [
            'executeCommand' => null,
            'getOutput' => new \Symfony\Component\Console\Output\NullOutput()
        ]);
    }

    // tests
    public function testPhpUnitRun()
    {
        $isWindows = defined('PHP_WINDOWS_VERSION_MAJOR');
        $command = $isWindows ? 'call vendor/bin/phpunit' : 'vendor/bin/phpunit';
        
        $this->taskPHPUnit()->run();
        $this->phpunit->verifyInvoked('executeCommand', [$command]);
    }

    public function testPHPUnitCommand()
    {
        $task = $this->taskPHPUnit('phpunit')
            ->bootstrap('bootstrap.php')
            ->filter('Model')
            ->group('important')
            ->xml('result.xml')
            ->debug();
        verify($task->getCommand())->equals('phpunit --bootstrap bootstrap.php --filter Model --group important --log-junit result.xml --debug');
        $task->run();
        $this->phpunit->verifyInvoked('executeCommand', ['phpunit --bootstrap bootstrap.php --filter Model --group important --log-junit result.xml --debug']);
    }

}