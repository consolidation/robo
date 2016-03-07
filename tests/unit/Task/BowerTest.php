<?php
use AspectMock\Test as test;
use Robo\Config;

class BowerTest extends \Codeception\TestCase\Test
{
    use \Robo\Task\Bower\loadTasks;
    use \Robo\TaskSupport;

    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $baseBower;

    protected function _before()
    {
        $this->baseBower = test::double('Robo\Task\Bower\Base', [
            'getOutput' => new \Symfony\Component\Console\Output\NullOutput()
        ]);
        $this->setTaskAssembler(new \Robo\TaskAssembler(Config::logger()));
    }
    // tests
    public function testBowerInstall()
    {
        $bower = test::double('Robo\Task\Bower\Install', ['executeCommand' => null]);
        $this->taskBowerInstall('bower')->run();
        $bower->verifyInvoked('executeCommand', ['bower install']);
    }

    public function testBowerUpdate()
    {
        $bower = test::double('Robo\Task\Bower\Update', ['executeCommand' => null]);
        $this->taskBowerUpdate('bower')->run();
        $bower->verifyInvoked('executeCommand', ['bower update']);
    }

    public function testBowerInstallCommand()
    {
        verify(
            $this->taskBowerInstall('bower')->getCommand()
        )->equals('bower install');

        verify(
            $this->taskBowerInstall('bower')->getCommand()
        )->equals('bower install');

        verify(
            $this->taskBowerInstall('bower')
                ->allowRoot()
                ->forceLatest()
                ->offline()
                ->noDev()
                ->getCommand()
        )->equals('bower install --allow-root --force-latest --offline --production');
    }

}
