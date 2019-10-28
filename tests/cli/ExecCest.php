<?php

class ExecCest
{
    // tests
    public function toExecLsCommand(CliGuy $I)
    {
        $command = strncasecmp(PHP_OS, 'WIN', 3) == 0 ? 'dir' : 'ls';
        $res = $I->taskExec($command)->interactive(false)->run();
        $I->assertContains(
            'src',
            $res->getMessage());
        $I->assertContains(
            'codeception.yml',
            $res->getMessage());
    }

    public function testMultipleEnvVars(CliGuy $I)
    {
        $task = $I->taskExec('env')->interactive(false);
        $task->env('FOO', 'BAR');
        $task->env('BAR', 'BAZ');
        $result = $task->run();
        // Verify that the text contains our environment variable.
        $I->assertContains(
            'FOO=BAR',
            $result->getMessage());
        $I->assertContains(
            'BAR=BAZ',
            $result->getMessage());

        // Now verify that we can reset a value that was previously set.
        $task = $I->taskExec('env')->interactive(false);
        $task->env('FOO', 'BAR');
        $task->env('FOO', 'BAZ');
        $result = $task->run();
        // Verify that the text contains the most recent environment variable.
        $I->assertContains(
            'FOO=BAZ',
            $result->getMessage());
    }

    public function testInheritEnv(CliGuy $I)
    {
        // Symfony < 3.2.1 does not inherit environment variables, so there's
        // nothing to test if the function doesn't exist.
        if (!method_exists('Symfony\Component\Process\Process', 'inheritEnvironmentVariables')) {
            throw new \PHPUnit_Framework_SkippedTestError(
                'Inheriting of environment variables is not supported.'
            );
        }
        // With no environment variables set, count how many environment
        // variables are present.
        $task = $I->taskExec('env | wc -l')->interactive(false);
        $result = $task->run();
        $start_count = (int) $result->getMessage();
        $I->assertGreaterThan(0, $start_count);

        // Verify that we get the same amount of environment variables with
        // another exec call.
        $task = $I->taskExec('env | wc -l')->interactive(false);
        $result = $task->run();
        $I->assertEquals(
            $start_count,
            (int) $result->getMessage());

        // Now run the same command, but this time add another environment
        // variable, and see if our count increases by one.
        $task = $I->taskExec('env | wc -l')->interactive(false);
        $task->env('FOO', 'BAR');
        $result = $task->run();
        $I->assertEquals($start_count + 1, (int) $result->getMessage());
    }
}
