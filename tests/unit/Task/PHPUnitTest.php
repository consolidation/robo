<?php
use AspectMock\Test as test;
use Robo\Robo;

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
        $this->container = Robo::getContainer();
        $this->container->addServiceProvider(\Robo\Task\Testing\loadTasks::getTestingServices());
    }

    // tests
    public function testPhpUnitRun()
    {
        $this->container->get('taskPhpUnit')->run();
        $this->phpunit->verifyInvoked('executeCommand');
    }

    public function testPHPUnitCommand()
    {
        $task = $this->container->get('taskPhpUnit', ['phpunit'])
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
