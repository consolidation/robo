<?php
namespace Robo\Task;

use Robo\Output;
use Robo\Task\Shared\CommandInterface;
use Robo\Task\Shared\DynamicConfig;
use Robo\Task\Shared\Executable;
use Robo\Task\Shared\TaskException;
use Robo\Task\Shared\TaskInterface;

trait Rsync
{
    protected function taskRsync()
    {
        return new Remote\Rsync();
    }
}
