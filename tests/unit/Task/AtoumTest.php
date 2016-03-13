<?php
use AspectMock\Test as test;

class AtoumTest extends \Codeception\TestCase\Test
{
    use \Robo\Task\Testing\loadTasks;
    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $atoum;

    protected function _before()
    {
        $this->atoum = test::double('Robo\Task\Testing\Atoum', [
            'executeCommand' => null,
            'getOutput' => new \Symfony\Component\Console\Output\NullOutput()
        ]);
    }

    public function testAtoumRun()
    {
        $isWindows = defined('PHP_WINDOWS_VERSION_MAJOR');
        $command = $isWindows ? 'vendor/bin/atoum.bat' : 'vendor/bin/atoum';

        $this->taskAtoum("vendor/bin/atoum")->run();
        $this->atoum->verifyInvoked('executeCommand', [$command]);
    }


    public function testAtoumCommand()
    {
        $task = $this->taskAtoum('atoum')
            ->bootstrap('bootstrap.php')
            ->tags("needDb")
            ->lightReport()
            ->tap()
            ->bootstrap('tests/bootstrap.php')
            ->configFile("config/dev.php")
            ->debug()
            ->files(array("path/to/file1.php", "path/to/file2.php"))
            ->directories("tests/units")
        ;
        verify($task->getCommand())->equals('atoum --bootstrap bootstrap.php --tags needDb --use-light-report --use-tap-report --bootstrap tests/bootstrap.php -c config/dev.php --debug --f path/to/file1.php --f path/to/file2.php --directories tests/units');
        $task->run();
        $this->atoum->verifyInvoked('executeCommand', ['atoum --bootstrap bootstrap.php --tags needDb --use-light-report --use-tap-report --bootstrap tests/bootstrap.php -c config/dev.php --debug --f path/to/file1.php --f path/to/file2.php --directories tests/units']);
    }
}
