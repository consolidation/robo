<?php
use AspectMock\Test as test;

class BowerTest extends \Codeception\TestCase\Test
{
    use \Robo\Task\Bower;

    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $baseBower;

    protected function _before()
    {
        $this->baseBower = test::double('Robo\Task\BaseBowerTask', [
            'getOutput' => new \Symfony\Component\Console\Output\NullOutput()
        ]);
    }
    // tests
    public function testBowerInstall()
    {
        $bower = test::double('Robo\Task\BowerInstallTask', ['executeCommand' => null]);
        $this->taskBowerInstall('bower')->run();
        $bower->verifyInvoked('executeCommand', ['bower install']);
    }

    public function testBowerUpdate()
    {
        $bower = test::double('Robo\Task\BowerUpdateTask', ['executeCommand' => null]);
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