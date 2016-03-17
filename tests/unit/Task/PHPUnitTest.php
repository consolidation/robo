<?php
use AspectMock\Test as test;
use Robo\Config;

class PHPUnitTest extends \Codeception\TestCase\Test
{
    protected $container;

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
        $this->container = Config::getContainer();
        $this->container->addServiceProvider(\Robo\Task\Testing\loadTasks::getTestingServices());
    }

    // tests
    public function testPhpUnitRun()
    {
        $isWindows = defined('PHP_WINDOWS_VERSION_MAJOR');
        $command = $isWindows ? 'call vendor/bin/phpunit' : 'vendor/bin/phpunit';

        $this->container->get('taskPHPUnit')->run();
        $this->phpunit->verifyInvoked('executeCommand', [$command]);
    }

    public function testPHPUnitCommand()
    {
        $task = $this->container->get('taskPHPUnit', ['phpunit'])
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
