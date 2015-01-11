<?php
namespace Robo\Task;

use Robo\Contract\CommandInterface;
use Robo\Contract\TaskInterface;

trait Codeception {
    protected function taskCodecept($pathToCodeception = '')
    {
        return new Testing\CodeceptRun($pathToCodeception);
    }
}
