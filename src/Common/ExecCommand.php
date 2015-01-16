<?php 
namespace Robo\Common;

use Robo\Result;
use Symfony\Component\Process\Process;

/**
 * This task is supposed to be executed as shell command.
 * You can specify working directory and if output is printed.
 */
trait ExecCommand
{
    protected $isPrinted = true;
    protected $workingDirectory;

    public function getPrinted()
    {
        return $this->isPrinted;
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
        if ($this->workingDirectory) {
            $process->setWorkingDirectory($this->workingDirectory);
        }
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