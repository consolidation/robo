<?php

namespace Robo\Common;

use Robo\Contract\TaskInterface;
use Robo\Robo;
use Robo\Task\BaseTask;

class ProcessExecutor
{
    use ExecTrait;
    use TaskIO; // uses LoggerAwareTrait and ConfigAwareTrait
    use ProgressIndicatorAwareTrait;

    public function __construct($process)
    {
        $this->process = $process;
        $this->logger = Robo::logger();
    }

    public function getCommand()
    {
        return $this->process->getCommandLine();
    }

    public function run()
    {
        return $this->execute($this->process);
    }
}
