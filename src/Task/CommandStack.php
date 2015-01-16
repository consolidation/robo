<?php

namespace Robo\Task;

use Robo\Common\ExecCommand;
use Robo\Contract\PrintedInterface;
use Robo\Result;
use Robo\Task\Exec;
use Robo\Contract\CommandInterface;
use Robo\Common\DynamicParams;
use Robo\Exception\TaskException;

abstract class CommandStack extends BaseTask implements CommandInterface, PrintedInterface
{
    use DynamicParams;
    use ExecCommand;

    protected $executable;
    protected $result;
    protected $exec = [];
    protected $stopOnFail = false;
    protected $workingDirectory;
    protected $isPrinted = true;

    public function getPrinted()
    {
        return $this->isPrinted;
    }

    public function getCommand()
    {
        return implode(' && ', $this->exec);
    }

    public function exec($command)
    {
        if (is_array($command)) {
            $command = implode(' ', array_filter($command));
        }

        $command = $this->executable . ' ' . $this->stripExecutableFromCommand($command);
        array_push($this->exec, trim($command));
        return $this;
    }

    /**
     * Should command output be printed
     *
     * @param $arg
     * @return $this
     */
    public function printed($arg)
    {
        if (is_bool($arg)) {
            $this->isPrinted = $arg;
        }
        return $this;
    }

    /**
     * changes working directory of command
     * @param $dir
     * @return $this
     */
    public function dir($dir)
    {
        $this->workingDirectory = $dir;
        return $this;
    }


    protected function stripExecutableFromCommand($command)
    {
        $command = trim($command);
        $executable = $this->executable . ' ';
        if (strpos($command, $executable) === 0) {
            $command = substr($command, strlen($executable));
        }
        return $command;
    }

    public function run()
    {
        if (empty($this->exec)) {
            throw new TaskException($this, 'You must add at least one command');
        }
        if (!$this->stopOnFail) {
            return $this->executeCommand($this->getCommand());
        }

        foreach ($this->exec as $command) {
            $result = $this->executeCommand($command);
            if (!$result->wasSuccessful()) {
                return $result;
            }
        }

        return Result::success($this);
    }
}
