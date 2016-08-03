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
        $this->container = Robo::getContainer();
        $this->container->addServiceProvider(\Robo\Task\Composer\loadTasks::getComposerServices());
    }
    // tests
    public function testComposerInstall()
    {
        $composer = test::double('Robo\Task\Composer\Install', ['executeCommand' => null]);

        $this->container->get('taskComposerInstall', ['composer'])->run();
        $composer->verifyInvoked('executeCommand', ['composer install']);

        $this->container->get('taskComposerInstall', ['composer'])
            ->preferSource()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer install --prefer-source']);

        $this->container->get('taskComposerInstall', ['composer'])
            ->optimizeAutoloader()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer install --optimize-autoloader']);
    }

    public function testComposerUpdate()
    {
        $composer = test::double('Robo\Task\Composer\Update', ['executeCommand' => null]);

        $this->container->get('taskComposerUpdate', ['composer'])->run();
        $composer->verifyInvoked('executeCommand', ['composer update']);

        $this->container->get('taskComposerUpdate', ['composer'])
            ->optimizeAutoloader()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer update --optimize-autoloader']);
    }

    public function testComposerDumpAutoload()
    {
        $composer = test::double('Robo\Task\Composer\DumpAutoload', ['executeCommand' => null]);

        $this->container->get('taskComposerDumpAutoload', ['composer'])->run();
        $composer->verifyInvoked('executeCommand', ['composer dump-autoload']);

        $this->container->get('taskComposerDumpAutoload', ['composer'])
            ->noDev()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer dump-autoload --no-dev']);

        $this->container->get('taskComposerDumpAutoload', ['composer'])
            ->optimize()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer dump-autoload --optimize']);

        $this->container->get('taskComposerDumpAutoload', ['composer'])
            ->optimize()
            ->noDev()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer dump-autoload --optimize --no-dev']);
    }

    public function testComposerValidate()
    {
        $composer = test::double('Robo\Task\Composer\Validate', ['executeCommand' => null]);

        $this->container->get('taskComposerValidate', ['composer'])->run();
        $composer->verifyInvoked('executeCommand', ['composer validate']);

        $this->container->get('taskComposerValidate', ['composer'])
            ->noCheckAll()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer validate --no-check-all']);

        $this->container->get('taskComposerValidate', ['composer'])
            ->noCheckLock()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer validate --no-check-lock']);

        $this->container->get('taskComposerValidate', ['composer'])
            ->noCheckPublish()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer validate --no-check-publish']);

        $this->container->get('taskComposerValidate', ['composer'])
            ->withDependencies()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer validate --with-dependencies']);

        $this->container->get('taskComposerValidate', ['composer'])
            ->strict()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer validate --strict']);
    }

    public function testComposerInstallCommand()
    {
        verify(
            $this->container->get('taskComposerInstall', ['composer'])->getCommand()
        )->equals('composer install');

        verify(
            $this->container->get('taskComposerInstall', ['composer'])
                ->noDev()
                ->preferDist()
                ->optimizeAutoloader()
                ->getCommand()
        )->equals('composer install --prefer-dist --no-dev --optimize-autoloader');
    }

    public function testComposerUpdateCommand()
    {
        verify(
            $this->container->get('taskComposerUpdate', ['composer'])->getCommand()
        )->equals('composer update');

        verify(
            $this->container->get('taskComposerUpdate', ['composer'])
                ->noDev()
                ->preferDist()
                ->getCommand()
        )->equals('composer update --prefer-dist --no-dev');

        verify(
            $this->container->get('taskComposerUpdate', ['composer'])
                ->noDev()
                ->preferDist()
                ->optimizeAutoloader()
                ->getCommand()
        )->equals('composer update --prefer-dist --no-dev --optimize-autoloader');
    }

    public function testComposerDumpAutoloadCommand()
    {
        verify(
            $this->container->get('taskComposerDumpAutoload', ['composer'])->getCommand()
        )->equals('composer dump-autoload');

        verify(
            $this->container->get('taskComposerDumpAutoload', ['composer'])
                ->noDev()
                ->getCommand()
        )->equals('composer dump-autoload --no-dev');

        verify(
            $this->container->get('taskComposerDumpAutoload', ['composer'])
                ->optimize()
                ->getCommand()
        )->equals('composer dump-autoload --optimize');

        verify(
            $this->container->get('taskComposerDumpAutoload', ['composer'])
                ->optimize()
                ->noDev()
                ->getCommand()
        )->equals('composer dump-autoload --optimize --no-dev');
    }

    public function testComposerValidateCommand()
    {
        verify(
            $this->container->get('taskComposerValidate', ['composer'])->getCommand()
        )->equals('composer validate');

        verify(
            $this->container->get('taskComposerValidate', ['composer'])
                ->noCheckAll()
                ->getCommand()
        )->equals('composer validate --no-check-all');

        verify(
            $this->container->get('taskComposerValidate', ['composer'])
                ->noCheckLock()
                ->getCommand()
        )->equals('composer validate --no-check-lock');

        verify(
            $this->container->get('taskComposerValidate', ['composer'])
                ->noCheckPublish()
                ->getCommand()
        )->equals('composer validate --no-check-publish');

        verify(
            $this->container->get('taskComposerValidate', ['composer'])
                ->withDependencies()
                ->getCommand()
        )->equals('composer validate --with-dependencies');

        verify(
            $this->container->get('taskComposerValidate', ['composer'])
                ->strict()
                ->getCommand()
        )->equals('composer validate --strict');

        verify(
            $this->container->get('taskComposerValidate', ['composer'])
                ->noCheckAll()
                ->noCheckLock()
                ->noCheckPublish()
                ->withDependencies()
                ->strict()
                ->getCommand()
        )->equals('composer validate --no-check-all --no-check-lock --no-check-publish --with-dependencies --strict');
    }

}
