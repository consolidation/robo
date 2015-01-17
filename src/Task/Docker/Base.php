<?php 
namespace Robo\Task\Docker;
use Robo\Common\ExecOneCommand;
use Robo\Task\BaseTask;

abstract class Base extends BaseTask
{
    use ExecOneCommand;

    public function run()
    {
        $this->printTaskInfo("Running <info>{$this->command}</info>");
        return $this->executeCommand($this->getCommand());
    }
} 