<?php

namespace Robo\Task;

use Robo\Result;
use Robo\Contract\TaskInterface;

trait Concat
{
    protected function taskConcat($files)
    {
        return new Base\Concat($files);
    }
}
