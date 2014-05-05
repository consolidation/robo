<?php

namespace Robo\Task\Shared;

use Robo\Task\Exec;
use Robo\Task\Shared\DynamicConfig;

class CommandStack implements CommandInterface, TaskInterface
{
    use DynamicConfig;
    use Exec;

    protected $executable;
    protected $result;
    protected $exec = [];
    protected $stopOnFail = false;

    public function getCommand()
    {
        return implode(' && ', $this->exec);
    }

    public function exec($command)
    {
        if (is_array($command)) {
            $command = implode(' ', array_filter($command));
        }

        array_push($this->exec, trim(ltrim($command, $this->executable)));
        return $this;
    }

    public function run()
    {
        if (!$this->stopOnFail) {
            return $this->taskExec($this->getCommand())->run();
        }

        foreach ($this->exec as $command) {
            $result = $this->taskExec($command)->run();
            if (!$result->wasSuccessful()) {
                return $result;
            }
        }

        return Result::success($this);
    }
}
