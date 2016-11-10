<?php

namespace Robo\Task;

use Robo\Common\ExecCommand;
use Robo\Contract\PrintedInterface;
use Robo\Result;
use Robo\Contract\CommandInterface;
use Robo\Common\DynamicParams;
use Robo\Exception\TaskException;

abstract class CommandStack extends BaseTask implements CommandInterface, PrintedInterface
{
    use ExecCommand;

    /**
     * @var string
     */
    protected $executable;

    protected $result;

    /**
     * @var string[]
     */
    protected $exec = [];

    /**
     * @var bool
     */
    protected $stopOnFail = false;

    /**
     * {@inheritdoc}
     */
    public function getCommand()
    {
        return implode(' && ', $this->exec);
    }

    /**
     * @param string $executable
     *
     * @return $this
     */
    public function executable($executable)
    {
        $this->executable = $executable;
        return $this;
    }

    /**
     * @param string|string[] $command
     *
     * @return $this
     */
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
     * @param bool $stopOnFail
     *
     * @return $this
     */
    public function stopOnFail($stopOnFail = true)
    {
        $this->stopOnFail = $stopOnFail;
        return $this;
    }

    public function result($result)
    {
        $this->result = $result;
        return $this;
    }

    /**
     * @param string $command
     *
     * @return string
     */
    protected function stripExecutableFromCommand($command)
    {
        $command = trim($command);
        $executable = $this->executable . ' ';
        if (strpos($command, $executable) === 0) {
            $command = substr($command, strlen($executable));
        }
        return $command;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if (empty($this->exec)) {
            throw new TaskException($this, 'You must add at least one command');
        }
        if (!$this->stopOnFail) {
            $this->printTaskInfo('{command}', ['command' => $this->getCommand()]);
            return $this->executeCommand($this->getCommand());
        }

        foreach ($this->exec as $command) {
            $this->printTaskInfo("Executing {command}", ['command' => $command]);
            $result = $this->executeCommand($command);
            if (!$result->wasSuccessful()) {
                return $result;
            }
        }

        return Result::success($this);
    }
}
