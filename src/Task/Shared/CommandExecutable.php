<?php 
namespace Robo\Task\Shared;

use Robo\Result;
use Symfony\Component\Process\Process;

trait CommandExecutable
{
    protected $isPrinted = true;

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
     * @param $command
     * @return Result
     */
    protected function executeCommand($command)
    {
        $process = new Process($command);
        $process->setTimeout(null);
        if ($this->isPrinted) {
            $process->run(function ($type, $buffer) {
                print $buffer;
            });
        } else {
            $process->run();
        }

		return new Result($this, $process->getExitCode(), $process->getOutput());
    }
} 