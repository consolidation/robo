<?php

use AspectMock\Test as test;

class SemVerTest extends \Codeception\TestCase\Test
{
    public function testSemver()
    {
        $semver = test::double('Robo\Task\Development\SemVer', ['dump' => null]);
        $res = (new \Robo\Task\Development\SemVer())
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
        $res = (new \Robo\Task\Development\SemVer())
            ->increment('patch')
            ->run();
        verify($res->getMessage())->equals('v0.0.1');
        $res = (new \Robo\Task\Development\SemVer())
            ->increment('minor')
            ->run();
        verify($res->getMessage())->equals('v0.1.0');
        $semver->verifyInvoked('dump');
    }

    public function testSemverIncrementMajorAfterIncrementedMinorAndPatch()
    {
        $semver = test::double('Robo\Task\Development\SemVer', ['dump' => null]);
        $res = (new \Robo\Task\Development\SemVer())
            ->increment('patch')
            ->run();
        verify($res->getMessage())->equals('v0.0.1');
        $res = (new \Robo\Task\Development\SemVer())
            ->increment('minor')
            ->run();
        verify($res->getMessage())->equals('v0.1.0');
        $res = (new \Robo\Task\Development\SemVer())
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
        $res = (new \Robo\Task\Development\SemVer())
            ->increment('wrongParameter');
    }

    public function testThrowsExceptionWhenSemverFileNotWriteable()
    {
        \PHPUnit_Framework_TestCase::setExpectedExceptionRegExp(
            'Robo\Exception\TaskException',
            '/Failed to write semver file./'
        );
        (new \Robo\Task\Development\SemVer('/.semver'))
            ->increment('major')
            ->run();
    }
}
