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
        $this->assertEquals(
            'v1.0.1-RC.1',
            $res->getMessage());
        $semver->verifyInvoked('dump');
    }

    public function testSemverIncrementMinorAfterIncrementedPatch()
    {
        $semver = test::double('Robo\Task\Development\SemVer', ['dump' => null]);
        $res = (new \Robo\Task\Development\SemVer())
            ->increment('patch')
            ->run();
        $this->assertEquals(
            'v0.0.1',
            $res->getMessage());
        $res = (new \Robo\Task\Development\SemVer())
            ->increment('minor')
            ->run();
        $this->assertEquals(
            'v0.1.0',
            $res->getMessage());
        $semver->verifyInvoked('dump');
    }

    public function testSemverIncrementMajorAfterIncrementedMinorAndPatch()
    {
        $semver = test::double('Robo\Task\Development\SemVer', ['dump' => null]);
        $res = (new \Robo\Task\Development\SemVer())
            ->increment('patch')
            ->run();
        $this->assertEquals(
            'v0.0.1',
            $res->getMessage());
        $res = (new \Robo\Task\Development\SemVer())
            ->increment('minor')
            ->run();
        $this->assertEquals(
            'v0.1.0',
            $res->getMessage());
        $res = (new \Robo\Task\Development\SemVer())
            ->increment('major')
            ->run();
        $this->assertEquals(
            'v1.0.0',
            $res->getMessage());
        $semver->verifyInvoked('dump');
    }

    public function testSemverParseFileWithWindowsLineEndings()
    {
        $fixturePath = tempnam(sys_get_temp_dir(), 'semver');
        $semverFile = str_replace("\n", "\r\n", file_get_contents(codecept_data_dir().'.semver'));
        file_put_contents($fixturePath, $semverFile);

        $res = (new \Robo\Task\Development\SemVer($fixturePath))
            ->run();
        $this->assertEquals(
            'v1.0.1-RC.1',
            $res->getMessage());
        @unlink($fixturePath);
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
