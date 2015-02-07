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
