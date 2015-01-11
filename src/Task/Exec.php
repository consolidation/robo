<?php
namespace Robo\Task;

use Robo\Output;
use Robo\Result;
use Robo\Task\Shared\CommandInterface;
use Robo\Task\Shared\CommandInjected;
use Robo\Task\Shared\CommandStack;
use Robo\Task\Shared\Executable;
use Robo\Task\Shared\TaskInterface;
use Symfony\Component\Process\Process;

/**
 * Task to execute shell scripts with `exec` command. Can be executed in background
 */
trait Exec  {
    private $runningCommands = [];
    protected function taskExec($command)
    {
        $exec = new Base\Exec($command);
        $this->runningCommands[] = $exec;
        return $exec;
    }

    protected function taskExecStack()
    {
        return new Base\ExecStack();
    }
}
