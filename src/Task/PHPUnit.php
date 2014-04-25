<?php
namespace Robo\Task;

use Robo\Result;
use Robo\Task\Shared\TaskInterface;

trait PHPUnit {
    protected function taskPHPUnit()
    {
        return new PHPUnitTask();
    }
}

class PHPUnitTask implements TaskInterface
{
    use \Robo\Output;

    public function __construct()
    {
        $this->command = 'phpunit';
    }

    public function run()
    {
        $this->printTaskInfo('Running PHPUnit');
        $line = system($this->command, $code);
        return new Result($this, $code, $line);
    }
}
