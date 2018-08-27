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
    }

    public function testInheritEnv(CliGuy $I)
    {
        // With no environment variables set, test that we have a known variable
        // such as PATH.
        $task = $I->taskExec('env | grep -E "^PATH="')->interactive(false);
        $result = $task->run();
        verify($result->getExitCode())->equals(\Robo\Result::EXITCODE_OK);

        // Now run the same command, but this time set an environment variable
        // on the task using ->env().
        $task = $I->taskExec('env | grep -E "^PATH="')->interactive(false);
        $task->env('FOO', 'BAR');
        $result = $task->run();
        verify($result->getExitCode())->equals(\Robo\Result::EXITCODE_OK);
    }
}
