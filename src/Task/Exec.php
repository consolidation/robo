<?php
namespace Robo\Task;

use Robo\Output;
use Robo\Result;
use Robo\Contract\CommandInterface;
use Robo\Common\CommandInjected;
use Robo\Task\CommandStack;
use Robo\Common\SingleExecutable;
use Robo\Contract\TaskInterface;
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
