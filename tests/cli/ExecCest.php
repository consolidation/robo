<?php

class ExecCest
{
    // tests
    public function toExecLsCommand(CliGuy $I)
    {
        $command = strncasecmp(PHP_OS, 'WIN', 3) == 0 ? 'dir' : 'ls';
        $res = $I->taskExec($command)->interactive(false)->run();
        verify($res->getMessage())->contains('src');
        verify($res->getMessage())->contains('codeception.yml');
    }

    public function testMultipleEnvVars(CliGuy $I)
    {
        $task = $I->taskExec('env')->interactive(false);
        $task->env('FOO', 'BAR');
        $task->env('BAR', 'BAZ');
        $result = $task->run();
        // Verify that the text contains our environment variable.
        verify($result->getMessage())->contains('FOO=BAR');
        verify($result->getMessage())->contains('BAR=BAZ');

        // Now verify that we can reset a value that was previously set.
        $task = $I->taskExec('env')->interactive(false);
        $task->env('FOO', 'BAR');
        $task->env('FOO', 'BAZ');
        $result = $task->run();
        // Verify that the text contains the most recent environment variable.
        verify($result->getMessage())->contains('FOO=BAZ');
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
        verify($start_count)->greaterThan(0);

        // Verify that we get the same amount of environment variables with
        // another exec call.
        $task = $I->taskExec('env | wc -l')->interactive(false);
        $result = $task->run();
        verify((int) $result->getMessage())->equals($start_count);

        // Now run the same command, but this time add another environment
        // variable, and see if our count increases by one.
        $task = $I->taskExec('env | wc -l')->interactive(false);
        $task->env('FOO', 'BAR');
        $result = $task->run();
        verify((int) $result->getMessage())->equals($start_count + 1);
    }
}
