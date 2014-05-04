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
        $this->taskBowerInstall()->run();
        $bower->verifyInvoked('executeCommand', ['/usr/bin/bower install ']);
    }

    public function testBowerUpdate()
    {
        $bower = test::double('Robo\Task\BowerUpdateTask', ['executeCommand' => null]);
        $this->taskBowerUpdate()->run();
        $bower->verifyInvoked('executeCommand', ['/usr/bin/bower update ']);
    }

    public function testBowerInstallCommand()
    {
        verify(
            trim($this->taskBowerInstall()->getCommand())
        )->equals('/usr/bin/bower install');

        verify(
            trim($this->taskBowerInstall('bower')->getCommand())
        )->equals('bower install');

        verify(
            $this->taskBowerInstall()
                ->allowRoot()
                ->forceLatest()
                ->offline()
                ->noDev()
                ->getCommand()
        )->equals('/usr/bin/bower install --allow-root --force-latest --offline --production');
    }

}