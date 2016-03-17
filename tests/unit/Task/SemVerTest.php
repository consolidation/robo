<?php

use AspectMock\Test as test;
use Robo\Config;

class SemVerTest extends \Codeception\TestCase\Test
{
    protected $container;

    protected function _before()
    {
        $this->container = Config::getContainer();
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
