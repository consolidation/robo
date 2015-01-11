<?php
namespace Robo\Task;

use Robo\Output;
use Robo\Contract\CommandInterface;
use Robo\Common\DynamicConfig;
use Robo\Common\SingleExecutable;
use Robo\Exception\TaskException;
use Robo\Contract\TaskInterface;

trait Rsync
{
    protected function taskRsync()
    {
        return new Remote\Rsync();
    }
}
