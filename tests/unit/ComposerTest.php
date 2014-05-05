<?php
use AspectMock\Test as test;

class ComposerTest extends \Codeception\TestCase\Test
{
    use \Robo\Task\Composer;

    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $baseComposer;

    protected function _before()
    {
        $this->baseComposer = test::double('Robo\Task\BaseComposerTask', [
            'getOutput' => new \Symfony\Component\Console\Output\NullOutput()
        ]);
    }
    // tests
    public function testComposerInstall()
    {
        $composer = test::double('Robo\Task\ComposerInstallTask', ['executeCommand' => null]);
        $this->taskComposerInstall('composer')->run();
        $composer->verifyInvoked('executeCommand', ['composer install ']);

        $this->taskComposerInstall('composer')
            ->preferSource()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer install --prefer-source']);
    }

    public function testComposerUpdate()
    {
        $composer = test::double('Robo\Task\ComposerUpdateTask', ['executeCommand' => null]);
        $this->taskComposerUpdate('composer')->run();
        $composer->verifyInvoked('executeCommand', ['composer update ']);
    }

    public function testComposerInstallCommand()
    {
        verify(
            trim($this->taskComposerInstall('composer')->getCommand())
        )->equals('composer install');

        verify(
            trim($this->taskComposerInstall('composer')->getCommand())
        )->equals('composer install');

        verify(
            $this->taskComposerInstall('composer')
                ->noDev()
                ->preferDist()
                ->getCommand()
        )->equals('composer install --prefer-dist --no-dev');
    }

}