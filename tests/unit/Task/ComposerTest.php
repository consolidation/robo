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
        $this->assertEquals(
            'composer install --no-interaction',
            (new \Robo\Task\Composer\Install('composer'))->setConfig(new \Robo\Config())->getCommand()
        );

        $this->assertEquals(
            'composer install --optimize-autoloader --prefer-dist --no-dev --no-interaction',
            (new \Robo\Task\Composer\Install('composer'))
                ->setConfig(new \Robo\Config())
                ->noDev()
                ->preferDist()
                ->optimizeAutoloader()
                ->getCommand()
        );
    }

    public function testComposerUpdateCommand()
    {
        $this->assertEquals(
            'composer update --no-interaction',
            (new \Robo\Task\Composer\Update('composer'))->setConfig(new \Robo\Config())->getCommand()
        );

        $this->assertEquals(
            'composer update --prefer-dist --no-dev --no-interaction',
            (new \Robo\Task\Composer\Update('composer'))
                ->setConfig(new \Robo\Config())
                ->noDev()
                ->preferDist()
                ->getCommand()
        );

        $this->assertEquals(
            'composer update --optimize-autoloader --prefer-dist --no-dev --no-interaction',
            (new \Robo\Task\Composer\Update('composer'))
                ->setConfig(new \Robo\Config())
                ->noDev()
                ->preferDist()
                ->optimizeAutoloader()
                ->getCommand()
        );
    }

    public function testComposerDumpAutoloadCommand()
    {
        $this->assertEquals(
            'composer dump-autoload --no-interaction',
            (new \Robo\Task\Composer\DumpAutoload('composer'))->setConfig(new \Robo\Config())->getCommand()
        );

        $this->assertEquals(
            'composer dump-autoload --no-dev --no-interaction',
            (new \Robo\Task\Composer\DumpAutoload('composer'))
                ->setConfig(new \Robo\Config())
                ->noDev()
                ->getCommand()
        );

        $this->assertEquals(
            'composer dump-autoload --optimize --no-interaction',
            (new \Robo\Task\Composer\DumpAutoload('composer'))
                ->setConfig(new \Robo\Config())
                ->optimize()
                ->getCommand()
        );

        $this->assertEquals(
            'composer dump-autoload --optimize --no-dev --no-interaction',
            (new \Robo\Task\Composer\DumpAutoload('composer'))
                ->setConfig(new \Robo\Config())
                ->optimize()
                ->noDev()
                ->getCommand()
        );
    }

    public function testComposerRemove()
    {
        $this->assertEquals(
            'composer remove --no-interaction',
            (new \Robo\Task\Composer\Remove('composer'))->setConfig(new \Robo\Config())->getCommand()
        );
        $this->assertEquals(
            'composer remove --dev --no-progress --no-update --no-interaction',
            (new \Robo\Task\Composer\Remove('composer'))
                ->setConfig(new \Robo\Config())
                ->dev()
                ->noProgress()
                ->noUpdate()
                ->getCommand()
        );
    }

    public function testComposerValidateCommand()
    {
        $this->assertEquals(
            'composer validate --no-interaction',
            (new \Robo\Task\Composer\Validate('composer'))->setConfig(new \Robo\Config())->getCommand()
        );

        $this->assertEquals(
            'composer validate --no-check-all --no-interaction',
            (new \Robo\Task\Composer\Validate('composer'))
                ->setConfig(new \Robo\Config())
                ->noCheckAll()
                ->getCommand()
        );

        $this->assertEquals(
            'composer validate --no-check-lock --no-interaction',
            (new \Robo\Task\Composer\Validate('composer'))
                ->setConfig(new \Robo\Config())
                ->noCheckLock()
                ->getCommand()
        );

        $this->assertEquals(
            'composer validate --no-check-publish --no-interaction',
            (new \Robo\Task\Composer\Validate('composer'))
                ->setConfig(new \Robo\Config())
                ->noCheckPublish()
                ->getCommand()
        );

        $this->assertEquals(
            'composer validate --with-dependencies --no-interaction',
            (new \Robo\Task\Composer\Validate('composer'))
                ->setConfig(new \Robo\Config())
                ->withDependencies()
                ->getCommand()
        );

        $this->assertEquals(
            'composer validate --strict --no-interaction',
            (new \Robo\Task\Composer\Validate('composer'))
                ->setConfig(new \Robo\Config())
                ->strict()
                ->getCommand()
        );

        $this->assertEquals(
            'composer validate --no-check-all --no-check-lock --no-check-publish --with-dependencies --strict --no-interaction',
            (new \Robo\Task\Composer\Validate('composer'))
                ->setConfig(new \Robo\Config())
                ->noCheckAll()
                ->noCheckLock()
                ->noCheckPublish()
                ->withDependencies()
                ->strict()
                ->getCommand()
        );
    }

    public function testComposerInitCommand()
    {
        $this->assertEquals(
            'composer init --no-interaction',
            (new \Robo\Task\Composer\Init('composer'))->setConfig(new \Robo\Config())->getCommand()
        );

        $this->assertEquals(
            $this->adjustQuotes("composer init --name foo/bar --description 'A test project' --require 'baz/boz:^2.4.8' --type project --homepage 'https://foo.bar.com' --stability beta --license MIT --repository 'https://packages.drupal.org/8' --no-interaction"),
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
        );

    }

    public function testComposerConfigCommand()
    {
        $this->assertEquals(
            "composer config bin-dir bin/ --no-interaction",
            (new \Robo\Task\Composer\Config('composer'))
                ->setConfig(new \Robo\Config())
                ->set('bin-dir', 'bin/')
                ->getCommand()
        );

        $this->assertEquals(
            "composer config --global bin-dir bin/ --no-interaction",
            (new \Robo\Task\Composer\Config('composer'))
                ->setConfig(new \Robo\Config())
                ->useGlobal()
                ->set('bin-dir', 'bin/')
                ->getCommand()
        );

        $this->assertEquals(
            $this->adjustQuotes("composer config repositories.drupalorg composer 'https://packages.drupal.org/8' --no-interaction"),
            (new \Robo\Task\Composer\Config('composer'))
                ->setConfig(new \Robo\Config())
                ->repository('drupalorg', 'https://packages.drupal.org/8', 'composer')
                ->getCommand()
        );
    }

    public function testComposerRequireCommand()
    {
        $this->assertEquals(
            $this->adjustQuotes("composer require 'foo/bar:^2.4.8' --no-interaction"),
            (new \Robo\Task\Composer\RequireDependency('composer'))
                ->setConfig(new \Robo\Config())
                ->dependency('foo/bar', '^2.4.8')
                ->getCommand()
        );

        $this->assertEquals(
            $this->adjustQuotes("composer require a/b 'x/y:^1' --no-interaction"),
            (new \Robo\Task\Composer\RequireDependency('composer'))
                ->setConfig(new \Robo\Config())
                ->dependency(['a/b', 'x/y:^1'])
                ->getCommand()
        );
    }

    public function testComposerCreateProjectCommand()
    {
        $this->assertEquals(
            $this->adjustQuotes("composer create-project --repository 'https://packages.drupal.org/8' --keep-vcs --no-install foo/bar mybar '^2.4.8' --no-interaction"),
            (new \Robo\Task\Composer\CreateProject('composer'))
                ->setConfig(new \Robo\Config())
                ->source('foo/bar')
                ->version('^2.4.8')
                ->target('mybar')
                ->repository('https://packages.drupal.org/8')
                ->keepVcs()
                ->noInstall()
                ->getCommand()
        );
    }

    public function testComposerCheckPlatformReqsCommand()
    {
        $this->assertEquals(
            $this->adjustQuotes("composer check-platform-reqs --no-interaction"),
            (new \Robo\Task\Composer\CheckPlatformReqs('composer'))
                ->setConfig(new \Robo\Config())
                ->getCommand()
        );
    }
}
