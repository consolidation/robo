<?php
namespace Robo\Task\Docker;

use Robo\Common\ExecOneCommand;
use Robo\Contract\PrintedInterface;
use Robo\Task\BaseTask;

abstract class Base extends BaseTask implements PrintedInterface
{
    use ExecOneCommand;

    public function run()
    {
        $this->printTaskInfo('Running {command}', ['command' => $this->getCommand()]);
        return $this->executeCommand($this->getCommand());
    }

    abstract public function getCommand();
}
