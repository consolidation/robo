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
            'output' => new \Symfony\Component\Console\Output\NullOutput(),
            'logger' => new \Psr\Log\NullLogger(),
        ]);
    }
    // tests
    public function testComposerInstall()
    {
        $composer = test::double('Robo\Task\Composer\Install', ['executeCommand' => null, 'getConfig' => new \Robo\Config(), 'logger' => new \Psr\Log\NullLogger()]);

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

    public function testComposerInstallAnsi()
    {
        $config = new \Robo\Config();
        $config->setDecorated(true);
        $composer = test::double('Robo\Task\Composer\Install', ['executeCommand' => null, 'getConfig' => $config, 'logger' => new \Psr\Log\NullLogger()]);

        (new \Robo\Task\Composer\Install('composer'))->run();
        $composer->verifyInvoked('executeCommand', ['composer install --ansi']);

        (new \Robo\Task\Composer\Install('composer'))
            ->preferSource()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer install --prefer-source --ansi']);

        (new \Robo\Task\Composer\Install('composer'))
            ->optimizeAutoloader()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer install --optimize-autoloader --ansi']);
    }

    public function testComposerUpdate()
    {
        $composer = test::double('Robo\Task\Composer\Update', ['executeCommand' => null, 'getConfig' => new \Robo\Config(), 'logger' => new \Psr\Log\NullLogger()]);

        (new \Robo\Task\Composer\Update('composer'))->run();
        $composer->verifyInvoked('executeCommand', ['composer update']);

        (new \Robo\Task\Composer\Update('composer'))
            ->optimizeAutoloader()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer update --optimize-autoloader']);
    }

    public function testComposerDumpAutoload()
    {
        $composer = test::double('Robo\Task\Composer\DumpAutoload', ['executeCommand' => null, 'getConfig' => new \Robo\Config(), 'logger' => new \Psr\Log\NullLogger()]);

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
        $composer = test::double('Robo\Task\Composer\Validate', ['executeCommand' => null, 'getConfig' => new \Robo\Config(), 'logger' => new \Psr\Log\NullLogger()]);

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
            (new \Robo\Task\Composer\Install('composer'))->setConfig(new \Robo\Config())->getCommand()
        )->equals('composer install');

        verify(
            (new \Robo\Task\Composer\Install('composer'))
                ->setConfig(new \Robo\Config())
                ->noDev()
                ->preferDist()
                ->optimizeAutoloader()
                ->getCommand()
        )->equals('composer install --prefer-dist --no-dev --optimize-autoloader');
    }

    public function testComposerUpdateCommand()
    {
        verify(
            (new \Robo\Task\Composer\Update('composer'))->setConfig(new \Robo\Config())->getCommand()
        )->equals('composer update');

        verify(
            (new \Robo\Task\Composer\Update('composer'))
                ->setConfig(new \Robo\Config())
                ->noDev()
                ->preferDist()
                ->getCommand()
        )->equals('composer update --prefer-dist --no-dev');

        verify(
            (new \Robo\Task\Composer\Update('composer'))
                ->setConfig(new \Robo\Config())
                ->noDev()
                ->preferDist()
                ->optimizeAutoloader()
                ->getCommand()
        )->equals('composer update --prefer-dist --no-dev --optimize-autoloader');
    }

    public function testComposerDumpAutoloadCommand()
    {
        verify(
            (new \Robo\Task\Composer\DumpAutoload('composer'))->setConfig(new \Robo\Config())->getCommand()
        )->equals('composer dump-autoload');

        verify(
            (new \Robo\Task\Composer\DumpAutoload('composer'))
                ->setConfig(new \Robo\Config())
                ->noDev()
                ->getCommand()
        )->equals('composer dump-autoload --no-dev');

        verify(
            (new \Robo\Task\Composer\DumpAutoload('composer'))
                ->setConfig(new \Robo\Config())
                ->optimize()
                ->getCommand()
        )->equals('composer dump-autoload --optimize');

        verify(
            (new \Robo\Task\Composer\DumpAutoload('composer'))
                ->setConfig(new \Robo\Config())
                ->optimize()
                ->noDev()
                ->getCommand()
        )->equals('composer dump-autoload --optimize --no-dev');
    }

    public function testComposerRemove()
    {
        verify(
            (new \Robo\Task\Composer\Remove('composer'))->setConfig(new \Robo\Config())->getCommand()
        )->equals('composer remove');
        verify(
            (new \Robo\Task\Composer\Remove('composer'))
                ->setConfig(new \Robo\Config())
                ->dev()
                ->noProgress()
                ->noUpdate()
                ->getCommand()
        )->equals('composer remove --dev --no-progress --no-update');
    }

    public function testComposerValidateCommand()
    {
        verify(
            (new \Robo\Task\Composer\Validate('composer'))->setConfig(new \Robo\Config())->getCommand()
        )->equals('composer validate');

        verify(
            (new \Robo\Task\Composer\Validate('composer'))
                ->setConfig(new \Robo\Config())
                ->noCheckAll()
                ->getCommand()
        )->equals('composer validate --no-check-all');

        verify(
            (new \Robo\Task\Composer\Validate('composer'))
                ->setConfig(new \Robo\Config())
                ->noCheckLock()
                ->getCommand()
        )->equals('composer validate --no-check-lock');

        verify(
            (new \Robo\Task\Composer\Validate('composer'))
                ->setConfig(new \Robo\Config())
                ->noCheckPublish()
                ->getCommand()
        )->equals('composer validate --no-check-publish');

        verify(
            (new \Robo\Task\Composer\Validate('composer'))
                ->setConfig(new \Robo\Config())
                ->withDependencies()
                ->getCommand()
        )->equals('composer validate --with-dependencies');

        verify(
            (new \Robo\Task\Composer\Validate('composer'))
                ->setConfig(new \Robo\Config())
                ->strict()
                ->getCommand()
        )->equals('composer validate --strict');

        verify(
            (new \Robo\Task\Composer\Validate('composer'))
                ->setConfig(new \Robo\Config())
                ->noCheckAll()
                ->noCheckLock()
                ->noCheckPublish()
                ->withDependencies()
                ->strict()
                ->getCommand()
        )->equals('composer validate --no-check-all --no-check-lock --no-check-publish --with-dependencies --strict');
    }
}
