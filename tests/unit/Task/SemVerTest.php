<?php

use AspectMock\Test as test;
use Robo\Runner;
use Robo\Container\RoboContainer;

class SemVerTest extends \Codeception\TestCase\Test
{
    protected $container;

    protected function _before()
    {
        $this->container = new RoboContainer();
        Runner::configureContainer($this->container);
        $this->container->addServiceProvider(\Robo\Task\Development\ServiceProvider::class);
    }

    public function testSemver()
    {
        $semver = test::double('Robo\Task\Development\SemVer', ['dump' => null]);
        $res = $this->container->get('taskSemVer')
            ->increment('major')
            ->prerelease('RC')
            ->increment('patch')
            ->run();
        verify($res->getMessage())->equals('v1.0.1-RC.1');
        $semver->verifyInvoked('dump');
    }

    public function testThrowsExceptionWhenSemverFileNotWriteable()
    {
        \PHPUnit_Framework_TestCase::setExpectedExceptionRegExp(
            'Robo\Exception\TaskException',
            '/Failed to write semver file./'
        );
        $this->container->get('taskSemVer', ['/.semver'])
            ->increment('major')
            ->run();
    }
}
