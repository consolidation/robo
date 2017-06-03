<?php
use AspectMock\Test as test;

use Robo\Traits\Common\AdjustQuotes;

class ComposerTest extends \Codeception\TestCase\Test
{
    use AdjustQuotes;

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
        $composer->verifyInvoked('executeCommand', ['composer install --no-interaction']);

        (new \Robo\Task\Composer\Install('composer'))
            ->preferSource()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer install --prefer-source --no-interaction']);

        (new \Robo\Task\Composer\Install('composer'))
            ->optimizeAutoloader()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer install --optimize-autoloader --no-interaction']);
    }

    public function testComposerInstallAnsi()
    {
        $config = new \Robo\Config();
        $config->setDecorated(true);
        $config->setInteractive(true);
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
        $composer->verifyInvoked('executeCommand', ['composer update --no-interaction']);

        (new \Robo\Task\Composer\Update('composer'))
            ->optimizeAutoloader()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer update --optimize-autoloader --no-interaction']);
    }

    public function testComposerDumpAutoload()
    {
        $composer = test::double('Robo\Task\Composer\DumpAutoload', ['executeCommand' => null, 'getConfig' => new \Robo\Config(), 'logger' => new \Psr\Log\NullLogger()]);

        (new \Robo\Task\Composer\DumpAutoload('composer'))->run();
        $composer->verifyInvoked('executeCommand', ['composer dump-autoload --no-interaction']);

        (new \Robo\Task\Composer\DumpAutoload('composer'))
            ->noDev()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer dump-autoload --no-dev --no-interaction']);

        (new \Robo\Task\Composer\DumpAutoload('composer'))
            ->optimize()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer dump-autoload --optimize --no-interaction']);

        (new \Robo\Task\Composer\DumpAutoload('composer'))
            ->optimize()
            ->noDev()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer dump-autoload --optimize --no-dev --no-interaction']);
    }

    public function testComposerValidate()
    {
        $composer = test::double('Robo\Task\Composer\Validate', ['executeCommand' => null, 'getConfig' => new \Robo\Config(), 'logger' => new \Psr\Log\NullLogger()]);

        (new \Robo\Task\Composer\Validate('composer'))->run();
        $composer->verifyInvoked('executeCommand', ['composer validate --no-interaction']);

        (new \Robo\Task\Composer\Validate('composer'))
            ->noCheckAll()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer validate --no-check-all --no-interaction']);

        (new \Robo\Task\Composer\Validate('composer'))
            ->noCheckLock()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer validate --no-check-lock --no-interaction']);

        (new \Robo\Task\Composer\Validate('composer'))
            ->noCheckPublish()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer validate --no-check-publish --no-interaction']);

        (new \Robo\Task\Composer\Validate('composer'))
            ->withDependencies()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer validate --with-dependencies --no-interaction']);

        (new \Robo\Task\Composer\Validate('composer'))
            ->strict()
            ->run();
        $composer->verifyInvoked('executeCommand', ['composer validate --strict --no-interaction']);
    }

    public function testComposerInstallCommand()
    {
        verify(
            (new \Robo\Task\Composer\Install('composer'))->setConfig(new \Robo\Config())->getCommand()
        )->equals('composer install --no-interaction');

        verify(
            (new \Robo\Task\Composer\Install('composer'))
                ->setConfig(new \Robo\Config())
                ->noDev()
                ->preferDist()
                ->optimizeAutoloader()
                ->getCommand()
        )->equals('composer install --optimize-autoloader --prefer-dist --no-dev --no-interaction');
    }

    public function testComposerUpdateCommand()
    {
        verify(
            (new \Robo\Task\Composer\Update('composer'))->setConfig(new \Robo\Config())->getCommand()
        )->equals('composer update --no-interaction');

        verify(
            (new \Robo\Task\Composer\Update('composer'))
                ->setConfig(new \Robo\Config())
                ->noDev()
                ->preferDist()
                ->getCommand()
        )->equals('composer update --prefer-dist --no-dev --no-interaction');

        verify(
            (new \Robo\Task\Composer\Update('composer'))
                ->setConfig(new \Robo\Config())
                ->noDev()
                ->preferDist()
                ->optimizeAutoloader()
                ->getCommand()
        )->equals('composer update --optimize-autoloader --prefer-dist --no-dev --no-interaction');
    }

    public function testComposerDumpAutoloadCommand()
    {
        verify(
            (new \Robo\Task\Composer\DumpAutoload('composer'))->setConfig(new \Robo\Config())->getCommand()
        )->equals('composer dump-autoload --no-interaction');

        verify(
            (new \Robo\Task\Composer\DumpAutoload('composer'))
                ->setConfig(new \Robo\Config())
                ->noDev()
                ->getCommand()
        )->equals('composer dump-autoload --no-dev --no-interaction');

        verify(
            (new \Robo\Task\Composer\DumpAutoload('composer'))
                ->setConfig(new \Robo\Config())
                ->optimize()
                ->getCommand()
        )->equals('composer dump-autoload --optimize --no-interaction');

        verify(
            (new \Robo\Task\Composer\DumpAutoload('composer'))
                ->setConfig(new \Robo\Config())
                ->optimize()
                ->noDev()
                ->getCommand()
        )->equals('composer dump-autoload --optimize --no-dev --no-interaction');
    }

    public function testComposerRemove()
    {
        verify(
            (new \Robo\Task\Composer\Remove('composer'))->setConfig(new \Robo\Config())->getCommand()
        )->equals('composer remove --no-interaction');
        verify(
            (new \Robo\Task\Composer\Remove('composer'))
                ->setConfig(new \Robo\Config())
                ->dev()
                ->noProgress()
                ->noUpdate()
                ->getCommand()
        )->equals('composer remove --dev --no-progress --no-update --no-interaction');
    }

    public function testComposerValidateCommand()
    {
        verify(
            (new \Robo\Task\Composer\Validate('composer'))->setConfig(new \Robo\Config())->getCommand()
        )->equals('composer validate --no-interaction');

        verify(
            (new \Robo\Task\Composer\Validate('composer'))
                ->setConfig(new \Robo\Config())
                ->noCheckAll()
                ->getCommand()
        )->equals('composer validate --no-check-all --no-interaction');

        verify(
            (new \Robo\Task\Composer\Validate('composer'))
                ->setConfig(new \Robo\Config())
                ->noCheckLock()
                ->getCommand()
        )->equals('composer validate --no-check-lock --no-interaction');

        verify(
            (new \Robo\Task\Composer\Validate('composer'))
                ->setConfig(new \Robo\Config())
                ->noCheckPublish()
                ->getCommand()
        )->equals('composer validate --no-check-publish --no-interaction');

        verify(
            (new \Robo\Task\Composer\Validate('composer'))
                ->setConfig(new \Robo\Config())
                ->withDependencies()
                ->getCommand()
        )->equals('composer validate --with-dependencies --no-interaction');

        verify(
            (new \Robo\Task\Composer\Validate('composer'))
                ->setConfig(new \Robo\Config())
                ->strict()
                ->getCommand()
        )->equals('composer validate --strict --no-interaction');

        verify(
            (new \Robo\Task\Composer\Validate('composer'))
                ->setConfig(new \Robo\Config())
                ->noCheckAll()
                ->noCheckLock()
                ->noCheckPublish()
                ->withDependencies()
                ->strict()
                ->getCommand()
        )->equals('composer validate --no-check-all --no-check-lock --no-check-publish --with-dependencies --strict --no-interaction');
    }

    public function testComposerInitCommand()
    {
        verify(
            (new \Robo\Task\Composer\Init('composer'))->setConfig(new \Robo\Config())->getCommand()
        )->equals('composer init --no-interaction');

        verify(
            (new \Robo\Task\Composer\Init('composer'))
                ->setConfig(new \Robo\Config())
                ->projectName('foo/bar')
                ->description('A test project')
                ->dependency('baz/boz', '^2.4.8')
                ->projectType('project')
                ->homepage('https://foo.bar.com')
                ->stability('beta')
                ->license('MIT')
                ->repository('https://packages.drupal.org/8')
                ->getCommand()
        )->equals($this->adjustQuotes("composer init --name foo/bar --description 'A test project' --require 'baz/boz:^2.4.8' --type project --homepage 'https://foo.bar.com' --stability beta --license MIT --repository 'https://packages.drupal.org/8' --no-interaction"));

    }

    public function testComposerConfigCommand()
    {
        verify(
            (new \Robo\Task\Composer\Config('composer'))
                ->setConfig(new \Robo\Config())
                ->set('bin-dir', 'bin/')
                ->getCommand()
        )->equals("composer config bin-dir bin/ --no-interaction");

        verify(
            (new \Robo\Task\Composer\Config('composer'))
                ->setConfig(new \Robo\Config())
                ->useGlobal()
                ->set('bin-dir', 'bin/')
                ->getCommand()
        )->equals("composer config --global bin-dir bin/ --no-interaction");

        verify(
            (new \Robo\Task\Composer\Config('composer'))
                ->setConfig(new \Robo\Config())
                ->repository('drupalorg', 'https://packages.drupal.org/8', 'composer')
                ->getCommand()
        )->equals($this->adjustQuotes("composer config repositories.drupalorg composer 'https://packages.drupal.org/8' --no-interaction"));
    }

    public function testComposerRequireCommand()
    {
        verify(
            (new \Robo\Task\Composer\RequireDependency('composer'))
                ->setConfig(new \Robo\Config())
                ->dependency('foo/bar', '^2.4.8')
                ->getCommand()
        )->equals($this->adjustQuotes("composer require 'foo/bar:^2.4.8' --no-interaction"));

        verify(
            (new \Robo\Task\Composer\RequireDependency('composer'))
                ->setConfig(new \Robo\Config())
                ->dependency(['a/b', 'x/y:^1'])
                ->getCommand()
        )->equals($this->adjustQuotes("composer require a/b 'x/y:^1' --no-interaction"));
    }

    public function testComposerCreateProjectCommand()
    {
        verify(
            (new \Robo\Task\Composer\CreateProject('composer'))
                ->setConfig(new \Robo\Config())
                ->source('foo/bar')
                ->version('^2.4.8')
                ->target('mybar')
                ->repository('https://packages.drupal.org/8')
                ->keepVcs()
                ->noInstall()
                ->getCommand()
        )->equals($this->adjustQuotes("composer create-project --repository 'https://packages.drupal.org/8' --keep-vcs --no-install foo/bar mybar '^2.4.8' --no-interaction"));
    }
}
