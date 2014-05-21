<?php

use AspectMock\Test as test;

class SemVerTest extends \Codeception\TestCase\Test
{
    use \Robo\Task\Development;

    public function testSemver()
    {
        $semver = test::double('Robo\Task\SemVerTask', ['dump' => null]);
        $res = $this->taskSemVer()
            ->increment('major')
            ->prerelease('RC')
            ->increment('patch')
            ->run();
        verify($res->getMessage())->equals('v1.0.1-RC.1');
        $semver->verifyInvoked('dump');
    }
} 