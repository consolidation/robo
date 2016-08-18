<?php
use AspectMock\Test as test;
use Robo\Robo;

class ComposerTest extends \Codeception\TestCase\Test
{
    protected $container;

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

        (new \Robo\Task\Composer\Install('composer'))->run();
        $composer->verifyInvoked('executeCommand', ['composer install']);

        (new \Robo\Task\Composer\Install('composer'))
            ->preferSource()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer install --prefer-source']);

        (new \Robo\Task\Composer\Install('composer'))
            ->optimizeAutoloader()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer install --optimize-autoloader']);
    }

    public function testComposerUpdate()
    {
        $composer = test::double('Robo\Task\Composer\Update', ['executeCommand' => null]);

        (new \Robo\Task\Composer\Update('composer'))->run();
        $composer->verifyInvoked('executeCommand', ['composer update']);

        (new \Robo\Task\Composer\Update('composer'))
            ->optimizeAutoloader()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer update --optimize-autoloader']);
    }

    public function testComposerDumpAutoload()
    {
        $composer = test::double('Robo\Task\Composer\DumpAutoload', ['executeCommand' => null]);

        (new \Robo\Task\Composer\DumpAutoload('composer'))->run();
        $composer->verifyInvoked('executeCommand', ['composer dump-autoload']);

        (new \Robo\Task\Composer\DumpAutoload('composer'))
            ->noDev()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer dump-autoload --no-dev']);

        (new \Robo\Task\Composer\DumpAutoload('composer'))
            ->optimize()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer dump-autoload --optimize']);

        (new \Robo\Task\Composer\DumpAutoload('composer'))
            ->optimize()
            ->noDev()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer dump-autoload --optimize --no-dev']);
    }

    public function testComposerValidate()
    {
        $composer = test::double('Robo\Task\Composer\Validate', ['executeCommand' => null]);

        (new \Robo\Task\Composer\Validate('composer'))->run();
        $composer->verifyInvoked('executeCommand', ['composer validate']);

        (new \Robo\Task\Composer\Validate('composer'))
            ->noCheckAll()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer validate --no-check-all']);

        (new \Robo\Task\Composer\Validate('composer'))
            ->noCheckLock()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer validate --no-check-lock']);

        (new \Robo\Task\Composer\Validate('composer'))
            ->noCheckPublish()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer validate --no-check-publish']);

        (new \Robo\Task\Composer\Validate('composer'))
            ->withDependencies()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer validate --with-dependencies']);

        (new \Robo\Task\Composer\Validate('composer'))
            ->strict()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer validate --strict']);
    }

    public function testComposerInstallCommand()
    {
        verify(
            (new \Robo\Task\Composer\Install('composer'))->getCommand()
        )->equals('composer install');

        verify(
            (new \Robo\Task\Composer\Install('composer'))
                ->noDev()
                ->preferDist()
                ->optimizeAutoloader()
                ->getCommand()
        )->equals('composer install --prefer-dist --no-dev --optimize-autoloader');
    }

    public function testComposerUpdateCommand()
    {
        verify(
            (new \Robo\Task\Composer\Update('composer'))->getCommand()
        )->equals('composer update');

        verify(
            (new \Robo\Task\Composer\Update('composer'))
                ->noDev()
                ->preferDist()
                ->getCommand()
        )->equals('composer update --prefer-dist --no-dev');

        verify(
            (new \Robo\Task\Composer\Update('composer'))
                ->noDev()
                ->preferDist()
                ->optimizeAutoloader()
                ->getCommand()
        )->equals('composer update --prefer-dist --no-dev --optimize-autoloader');
    }

    public function testComposerDumpAutoloadCommand()
    {
        verify(
            (new \Robo\Task\Composer\DumpAutoload('composer'))->getCommand()
        )->equals('composer dump-autoload');

        verify(
            (new \Robo\Task\Composer\DumpAutoload('composer'))
                ->noDev()
                ->getCommand()
        )->equals('composer dump-autoload --no-dev');

        verify(
            (new \Robo\Task\Composer\DumpAutoload('composer'))
                ->optimize()
                ->getCommand()
        )->equals('composer dump-autoload --optimize');

        verify(
            (new \Robo\Task\Composer\DumpAutoload('composer'))
                ->optimize()
                ->noDev()
                ->getCommand()
        )->equals('composer dump-autoload --optimize --no-dev');
    }

    public function testComposerValidateCommand()
    {
        verify(
            (new \Robo\Task\Composer\Validate('composer'))->getCommand()
        )->equals('composer validate');

        verify(
            (new \Robo\Task\Composer\Validate('composer'))
                ->noCheckAll()
                ->getCommand()
        )->equals('composer validate --no-check-all');

        verify(
            (new \Robo\Task\Composer\Validate('composer'))
                ->noCheckLock()
                ->getCommand()
        )->equals('composer validate --no-check-lock');

        verify(
            (new \Robo\Task\Composer\Validate('composer'))
                ->noCheckPublish()
                ->getCommand()
        )->equals('composer validate --no-check-publish');

        verify(
            (new \Robo\Task\Composer\Validate('composer'))
                ->withDependencies()
                ->getCommand()
        )->equals('composer validate --with-dependencies');

        verify(
            (new \Robo\Task\Composer\Validate('composer'))
                ->strict()
                ->getCommand()
        )->equals('composer validate --strict');

        verify(
            (new \Robo\Task\Composer\Validate('composer'))
                ->noCheckAll()
                ->noCheckLock()
                ->noCheckPublish()
                ->withDependencies()
                ->strict()
                ->getCommand()
        )->equals('composer validate --no-check-all --no-check-lock --no-check-publish --with-dependencies --strict');
    }

}
