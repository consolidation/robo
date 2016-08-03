<?php

use AspectMock\Test as test;
use Robo\Robo;

class SemVerTest extends \Codeception\TestCase\Test
{
    protected $container;

    protected function _before()
    {
        $this->container = Robo::getContainer();
        $this->container->addServiceProvider(\Robo\Task\Development\loadTasks::getDevelopmentServices());
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

    public function testSemverIncrementMinorAfterIncrementedPatch()
    {
        $semver = test::double('Robo\Task\Development\SemVer', ['dump' => null]);
        $res = $this->container->get('taskSemVer')
            ->increment('patch')
            ->run();
        verify($res->getMessage())->equals('v0.0.1');
        $res = $this->container->get('taskSemVer')
            ->increment('minor')
            ->run();
        verify($res->getMessage())->equals('v0.1.0');
        $semver->verifyInvoked('dump');
    }

    public function testSemverIncrementMajorAfterIncrementedMinorAndPatch()
    {
        $semver = test::double('Robo\Task\Development\SemVer', ['dump' => null]);
        $res = $this->container->get('taskSemVer')
            ->increment('patch')
            ->run();
        verify($res->getMessage())->equals('v0.0.1');
        $res = $this->container->get('taskSemVer')
            ->increment('minor')
            ->run();
        verify($res->getMessage())->equals('v0.1.0');
        $res = $this->container->get('taskSemVer')
            ->increment('major')
            ->run();
        verify($res->getMessage())->equals('v1.0.0');
        $semver->verifyInvoked('dump');
    }

    public function testThrowsExceptionWhenIncrementWithWrongParameter()
    {
        \PHPUnit_Framework_TestCase::setExpectedExceptionRegExp(
            'Robo\Exception\TaskException',
            '/Bad argument, only one of the following is allowed: major, minor, patch/'
        );
        $res = $this->container->get('taskSemVer')
            ->increment('wrongParameter');
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
