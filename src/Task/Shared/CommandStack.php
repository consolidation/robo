<?php

namespace Robot\Task\Shared;

use Robo\Task\Exec;

class CommandStack implements CommandInterface
{
    use Exec;

    protected $executable;

    protected $result;

    protected $stack = [];

    protected $stopOnFail = false;

    public function setExecutable($executable)
    {
        $this->executable = $executable;
        return $this;
    }

    public function stopOnFail()
    {
        $this->stopOnFail = true;
        return $this;
    }

    public function getCommand()
    {
        return implode(' && ', $this->stack);
    }

    public function pushCommand($command)
    {
        if (is_array($command)) {
            $command = implode(' ', array_filter($command));
        }

        array_push($this->stack, trim(ltrim($command, $this->executable)));
        return $this;
    }

    public function run()
    {
        if (!$this->stopOnFail) {
            return $this->taskExec($this->getCommand())->run();
        }

        foreach ($this->stack as $command) {
            $result = $this->taskExec($command)->run();
            if (!$result->wasSuccessful()) {
                return $result;
            }
        }

        return Result::success($this);
    }
}
