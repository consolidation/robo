<?php
namespace Robo\Task;

use Robo\Task\Shared\CommandInterface;
use Robo\Task\Shared\TaskInterface;

trait Codeception {
    protected function taskCodecept($pathToCodeception = '')
    {
        return new Testing\CodeceptRun($pathToCodeception);
    }
}
