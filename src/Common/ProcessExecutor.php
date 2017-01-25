<?php

namespace Robo\Common;

use Robo\Robo;
use Robo\Task\BaseTask;

class ProcessExecutor extends BaseTask
{
    use ExecTrait;

    /** @var string */
    protected $command;

    public function __construct($command)
    {
        $this->command = $command;
        $this->logger = Robo::logger();
    }

    public function getCommand()
    {
        return $this->command;
    }
}
