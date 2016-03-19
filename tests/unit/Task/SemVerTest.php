<?php

use AspectMock\Test as test;

class SemVerTest extends \Codeception\TestCase\Test
{
    use \Robo\Task\Development\loadTasks;

    public function testSemver()
    {
        $semver = test::double('Robo\Task\Development\SemVer', ['dump' => null]);
        $res = $this->taskSemVer()
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
        $res = $this->taskSemVer()
            ->increment('patch')
            ->run();
        verify($res->getMessage())->equals('v0.0.1');
        $res = $this->taskSemVer()
            ->increment('minor')
            ->run();
        verify($res->getMessage())->equals('v0.1.0');
        $semver->verifyInvoked('dump');
    }

    public function testSemverIncrementMajorAfterIncrementedMinorAndPatch()
    {
        $semver = test::double('Robo\Task\Development\SemVer', ['dump' => null]);
        $res = $this->taskSemVer()
            ->increment('patch')
            ->run();
        verify($res->getMessage())->equals('v0.0.1');
        $res = $this->taskSemVer()
            ->increment('minor')
            ->run();
        verify($res->getMessage())->equals('v0.1.0');
        $res = $this->taskSemVer()
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
        $this->taskSemVer()
            ->increment('wrongParameter');
    }

    public function testThrowsExceptionWhenSemverFileNotWriteable()
    {
        \PHPUnit_Framework_TestCase::setExpectedExceptionRegExp(
            'Robo\Exception\TaskException',
            '/Failed to write semver file./'
        );
        $this->taskSemVer('/.semver')
            ->increment('major')
            ->run();
    }
}
