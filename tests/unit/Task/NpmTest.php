<?php
use AspectMock\Test as test;
use Robo\Config;

class NpmTest extends \Codeception\TestCase\Test
{
    use \Robo\Task\Npm\loadTasks;
    use \Robo\TaskSupport;

    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $baseNpm;

    protected function _before()
    {
        $this->baseNpm = test::double('Robo\Task\Npm\Base', [
            'getOutput' => new \Symfony\Component\Console\Output\NullOutput()
        ]);
        $this->setTaskAssembler(new \Robo\TaskAssembler(Config::logger()));
    }
    // tests
    public function testNpmInstall()
    {
        $npm = test::double('Robo\Task\Npm\Install', ['executeCommand' => null]);
        $this->taskNpmInstall('npm')->run();
        $npm->verifyInvoked('executeCommand', ['npm install']);
    }

    public function testNpmUpdate()
    {
        $npm = test::double('Robo\Task\Npm\Update', ['executeCommand' => null]);
        $this->taskNpmUpdate('npm')->run();
        $npm->verifyInvoked('executeCommand', ['npm update']);
    }

    public function testNpmInstallCommand()
    {
        verify(
            $this->taskNpmInstall('npm')->getCommand()
        )->equals('npm install');

        verify(
            $this->taskNpmInstall('npm')->getCommand()
        )->equals('npm install');

        verify(
            $this->taskNpmInstall('npm')
                ->noDev()
                ->getCommand()
        )->equals('npm install --production');
    }

}
