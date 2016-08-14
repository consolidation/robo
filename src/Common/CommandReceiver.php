<?php
namespace Robo\Common;

use Robo\Contract\CommandInterface;
use Robo\Exception\TaskException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * This task can receive commands from task implementing CommandInterface.
 * It is also permissible to pass in a prepared command string, a
 * Symfony\Component\Process\ProcessBuilder object, or a constructed
 * Symfony\Component\Process\Process object.
 */
trait CommandReceiver
{
    /**
     * @param $command
     * @throws \Robo\Exception\TaskException
     * @return Symfony\Component\Process\Process
     */
    protected function receiveCommand($command)
    {
        if ($command instanceof CommandInterface) {
            $command = $command->getCommand();
        }
        if (is_string($command)) {
            $command = new Process(trim($command));
        }
        if (is_array($command)) {
            $command = new ProcessBuilder(array_filter($command));
        }
        if ($command instanceof ProcessBuilder) {
            $command = $command->getProcess();
        }

        if (!$command instanceof Process) {
            throw new TaskException($this, get_class($command) . " does not implement CommandInterface, so can't be passed into this task");
        }

        return $command;
    }
}
