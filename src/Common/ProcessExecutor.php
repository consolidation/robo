<?php

namespace Robo\Common;

use Robo\Contract\TaskInterface;
use Robo\Robo;
use Robo\Task\BaseTask;

class ProcessExecutor implements TaskInterface
{
    use ExecTrait;
    use TaskIO; // uses LoggerAwareTrait and ConfigAwareTrait
    use ProgressIndicatorAwareTrait;

    public function __construct($command)
    {
        $this->command = $command;
        $this->logger = Robo::logger();
    }
}
