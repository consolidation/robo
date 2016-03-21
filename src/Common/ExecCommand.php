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
    use Timer;

    protected $isPrinted = true;
    protected $workingDirectory;

    /**
     * Is command printing its output to screen
     * @return bool
     */
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
     * Look for a "{$cmd}.phar" in the current working
     * directory; return a string to exec it if it is
     * found.  Otherwise, look for an executable command
     * of the same name via findExecutable.
     */
    protected function findExecutablePhar($cmd)
    {
        if (file_exists("{$cmd}.phar"))
        {
            return "php {$cmd}.phar";
        }
        return $this->findExecutable($cmd);
    }

    /**
     * Return the best path to the executable program
     * with the provided name.  Favor vendor/bin in the
     * current project, or the global vendor/bin next.
     * If not found in either of these locations, use
     * whatever is on the $PATH.
     */
    protected function findExecutable($cmd)
    {
        if (is_executable("vendor/bin/{$cmd}")) {
            return $this->useCallOnWindows("vendor/bin/{$cmd}");
        }
        $home = array_key_exists('HOME', $_SERVER) ? $_SERVER['HOME'] : getenv('HOME');
        if ($home && is_executable("$home/vendor/bin/{$cmd}")) {
            return $this->useCallOnWindows("$home/vendor/bin/{$cmd}");
        }
        $pathToCmd = exec("which $cmd");
        if ($pathToCmd) {
            return $pathToCmd;
        }
        return false;
    }

    /**
     * Wrap Windows executables in 'call' per 7a88757d
     */
    protected function useCallOnWindows($cmd)
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            if (file_exists("{$cmd}.bat")) {
                $cmd = "{$cmd}.bat";
            }
            return "call $cmd";
        }
        return $cmd;
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
        $this->startTimer();
        if ($this->isPrinted) {
            $process->run(function ($type, $buffer) {
                print $buffer;
            });
        } else {
            $process->run();
        }
        $this->stopTimer();

        return new Result($this, $process->getExitCode(), $process->getOutput(), ['time' => $this->getExecutionTime()]);
    }
}
