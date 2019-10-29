<?php
use AspectMock\Test as test;

class BowerTest extends \Codeception\TestCase\Test
{
    protected $container;

    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $baseBower;

    protected function _before()
    {
        $this->baseBower = test::double('Robo\Task\Bower\Base', [
            'output' => new \Symfony\Component\Console\Output\NullOutput(),
            'logger' => new \Psr\Log\NullLogger(),
        ]);
    }
    // tests
    public function testBowerInstall()
    {
        $bower = test::double('Robo\Task\Bower\Install', ['executeCommand' => null, 'logger' => new \Psr\Log\NullLogger(),]);
        (new \Robo\Task\Bower\Install('bower'))->run();
        $bower->verifyInvoked('executeCommand', ['bower install']);
    }

    public function testBowerUpdate()
    {
        $bower = test::double('Robo\Task\Bower\Update', ['executeCommand' => null]);
        $task = new \Robo\Task\Bower\Update('bower');
        $task->setLogger(new \Psr\Log\NullLogger());

        $task->run();
        $bower->verifyInvoked('executeCommand', ['bower update']);
    }

    public function testBowerInstallCommand()
    {
        $this->assertEquals(
            'bower install',
            (new \Robo\Task\Bower\Install('bower'))->getCommand()
        );

        $this->assertEquals(
            'bower update',
            (new \Robo\Task\Bower\Update('bower'))->getCommand()
        );

        $this->assertEquals(
            'bower install --allow-root --force-latest --offline --production',
            (new \Robo\Task\Bower\Install('bower'))
                ->allowRoot()
                ->forceLatest()
                ->offline()
                ->noDev()
                ->getCommand()
        );
    }

}
