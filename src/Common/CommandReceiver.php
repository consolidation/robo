<?php
namespace Robo\Common;

use Robo\Contract\CommandInterface;
use Robo\Exception\TaskException;

/**
 * This task can receive commands from task implementing CommandInterface.
 */
trait CommandReceiver
{
    /**
     * @param $command
     * @throws \Robo\Exception\TaskException
     * @return string $command
     */
    protected function receiveCommand($command)
    {
        if (!is_object($command)) {
            return $command;
        }
        if ($command instanceof CommandInterface) {
            return $command->getCommand();
        } else {
            throw new TaskException($this, get_class($command) . " does not implement CommandInterface, so can't be passed into this task");
        }

    }
} 