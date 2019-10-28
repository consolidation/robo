<?php
use AspectMock\Test as test;

class NpmTest extends \Codeception\TestCase\Test
{
    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $baseNpm;

    protected function _before()
    {
        $this->baseNpm = test::double('Robo\Task\Npm\Base', [
            'output' => new \Symfony\Component\Console\Output\NullOutput(),
            'logger' => new \Psr\Log\NullLogger(),
        ]);
    }

    // tests
    public function testNpmInstall()
    {
        $npm = test::double('Robo\Task\Npm\Install', ['executeCommand' => null, 'logger' => new \Psr\Log\NullLogger()]);
        (new \Robo\Task\Npm\Install('npm'))->run();
        $npm->verifyInvoked('executeCommand', ['npm install']);
    }

    public function testNpmUpdate()
    {
        $npm = test::double('Robo\Task\Npm\Update', ['executeCommand' => null, 'logger' => new \Psr\Log\NullLogger()]);
        (new \Robo\Task\Npm\Update('npm'))->run();
        $npm->verifyInvoked('executeCommand', ['npm update']);
    }

    public function testNpmInstallCommand()
    {
        $this->assertEquals(
            'npm install',
            (new \Robo\Task\Npm\Install('npm'))->getCommand()
        );

        $this->assertEquals(
            'npm install --production',
            (new \Robo\Task\Npm\Install('npm'))
                ->noDev()
                ->getCommand()
        );
    }

}
