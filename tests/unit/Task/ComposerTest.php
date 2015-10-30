<?php
use AspectMock\Test as test;

class ComposerTest extends \Codeception\TestCase\Test
{
    use \Robo\Task\Composer\loadTasks;

    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $baseComposer;

    protected function _before()
    {
        $this->baseComposer = test::double('Robo\Task\Composer\Base', [
            'getOutput' => new \Symfony\Component\Console\Output\NullOutput()
        ]);
    }
    // tests
    public function testComposerInstall()
    {
        $composer = test::double('Robo\Task\Composer\Install', ['executeCommand' => null]);
        
        $this->taskComposerInstall('composer')->run();
        $composer->verifyInvoked('executeCommand', ['composer install']);

        $this->taskComposerInstall('composer')
            ->preferSource()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer install --prefer-source']);

        $this->taskComposerInstall('composer')
            ->optimizeAutoloader()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer install --optimize-autoloader']);
    }

    public function testComposerUpdate()
    {
        $composer = test::double('Robo\Task\Composer\Update', ['executeCommand' => null]);
        
        $this->taskComposerUpdate('composer')->run();
        $composer->verifyInvoked('executeCommand', ['composer update']);

        $this->taskComposerUpdate('composer')
            ->optimizeAutoloader()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer update --optimize-autoloader']);
    }

    public function testComposerDumpAutoload()
    {
        $composer = test::double('Robo\Task\Composer\DumpAutoload', ['executeCommand' => null]);
        
        $this->taskComposerDumpAutoload('composer')->run();
        $composer->verifyInvoked('executeCommand', ['composer dump-autoload']);

        $this->taskComposerDumpAutoload('composer')
            ->noDev()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer dump-autoload --no-dev']);

        $this->taskComposerDumpAutoload('composer')
            ->optimize()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer dump-autoload --optimize']);

        $this->taskComposerDumpAutoload('composer')
            ->optimize()
            ->noDev()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer dump-autoload --optimize --no-dev']);
    }

    public function testComposerInstallCommand()
    {
        verify(
            $this->taskComposerInstall('composer')->getCommand()
        )->equals('composer install');

        verify(
            $this->taskComposerInstall('composer')
                ->noDev()
                ->preferDist()
                ->optimizeAutoloader()
                ->getCommand()
        )->equals('composer install --prefer-dist --no-dev --optimize-autoloader');
    }

    public function testComposerUpdateCommand()
    {
        verify(
            $this->taskComposerUpdate('composer')->getCommand()
        )->equals('composer update');

        verify(
            $this->taskComposerUpdate('composer')
                ->noDev()
                ->preferDist()
                ->getCommand()
        )->equals('composer update --prefer-dist --no-dev');

        verify(
            $this->taskComposerUpdate('composer')
                ->noDev()
                ->preferDist()
                ->optimizeAutoloader()
                ->getCommand()
        )->equals('composer update --prefer-dist --no-dev --optimize-autoloader');
    }

    public function testComposerDumpAutoloadCommand()
    {
        verify(
            $this->taskComposerDumpAutoload('composer')->getCommand()
        )->equals('composer dump-autoload');

        verify(
            $this->taskComposerDumpAutoload('composer')
                ->noDev()
                ->getCommand()
        )->equals('composer dump-autoload --no-dev');

        verify(
            $this->taskComposerDumpAutoload('composer')
                ->optimize()
                ->getCommand()
        )->equals('composer dump-autoload --optimize');

        verify(
            $this->taskComposerDumpAutoload('composer')
                ->optimize()
                ->noDev()
                ->getCommand()
        )->equals('composer dump-autoload --optimize --no-dev');
    }

}