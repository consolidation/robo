<?php
use AspectMock\Test as test;

class AtoumTest extends \Codeception\TestCase\Test
{
    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $atoum;

    protected function _before()
    {
        $this->atoum = test::double('Robo\Task\Testing\Atoum', [
            'executeCommand' => null,
            'output' => new \Symfony\Component\Console\Output\NullOutput(),
            'logger' => new \Psr\Log\NullLogger(),
        ]);
    }

    public function testAtoumCommand()
    {
        $expectedCommand = implode(' ', [
            'atoum',
            '--bootstrap bootstrap.php',
            '--tags needDb',
            '--use-light-report',
            '--use-tap-report',
            '--bootstrap tests/bootstrap.php',
            '-c config/dev.php',
            '--debug',
            '--f path/to/file1.php',
            '--f path/to/file2.php',
            '--directories tests/units',
        ]);

        $task = (new \Robo\Task\Testing\Atoum('atoum'))
            ->bootstrap('bootstrap.php')
            ->tags("needDb")
            ->lightReport()
            ->tap()
            ->bootstrap('tests/bootstrap.php')
            ->configFile("config/dev.php")
            ->debug()
            ->files(array("path/to/file1.php", "path/to/file2.php"))
            ->directories("tests/units");

        $this->assertEquals($expectedCommand, $task->getCommand());

        $task->run();
        $this->atoum->verifyInvoked(
            'executeCommand',
            [
                $expectedCommand,
            ]
        );
    }
}
