<?php

namespace Robo\Task;

use Robo\Result;
use Robo\Task\Shared\TaskInterface;

trait Concat
{
    protected function taskConcat($files)
    {
        return new Base\Concat($files);
    }
}
