<?php
namespace Robo\Common;

use Robo\Result;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * This task is supposed to be executed as shell command.
 * You can specify working directory and if output is printed.
 */
trait ExecCommand
{
    /**
     * @var bool
     */
    protected $isPrinted = true;

    /**
     * @var string
     */
    protected $workingDirectory;

    /**
     * @var \Robo\Common\TimeKeeper
     */
    protected $execTimer;

    /**
     * @return \Robo\Common\TimeKeeper
     */
    protected function getExecTimer()
    {
        if (!isset($this->execTimer)) {
            $this->execTimer = new TimeKeeper();
        }
        return $this->execTimer;
    }

    /**
     * Is command printing its output to screen
     *
     * @return bool
     */
    public function getPrinted()
    {
        return $this->isPrinted;
    }

    /**
     * Changes working directory of command
     *
     * @param string $dir
     *
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
     * @param bool $arg
     *
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
     *
     * @param string $cmd
     *
     * @return bool|string
     */
    protected function findExecutablePhar($cmd)
    {
        if (file_exists("{$cmd}.phar")) {
            return "php {$cmd}.phar";
        }
        return $this->findExecutable($cmd);
    }

    /**
     * Return the best path to the executable program
     * with the provided name.  Favor vendor/bin in the
     * current project. If not found there, use
     * whatever is on the $PATH.
     *
     * @param string $cmd
     *
     * @return bool|string
     */
    protected function findExecutable($cmd)
    {
        $pathToCmd = $this->searchForExecutable($cmd);
        if ($pathToCmd) {
            return $this->useCallOnWindows($pathToCmd);
        }
        return false;
    }

    /**
     * @param string $cmd
     *
     * @return string
     */
    private function searchForExecutable($cmd)
    {
        $projectBin = $this->findProjectBin();

        $localComposerInstallation = $projectBin . DIRECTORY_SEPARATOR . $cmd;
        if (file_exists($localComposerInstallation)) {
            return $localComposerInstallation;
        }
        $finder = new ExecutableFinder();
        return $finder->find($cmd, null, []);
    }

    /**
     * @return bool|string
     */
    protected function findProjectBin()
    {
        $candidates = [ __DIR__ . '/../../vendor/bin', __DIR__ . '/../../bin' ];

        // If this project is inside a vendor directory, give highest priority
        // to that directory.
        $vendorDirContainingUs = realpath(__DIR__ . '/../../../..');
        if (is_dir($vendorDirContainingUs) && (basename($vendorDirContainingUs) == 'vendor')) {
            array_unshift($candidates, $vendorDirContainingUs . '/bin');
        }

        foreach ($candidates as $dir) {
            if (is_dir("$dir")) {
                return realpath($dir);
            }
        }
        return false;
    }

    /**
     * Wrap Windows executables in 'call' per 7a88757d
     *
     * @param string $cmd
     *
     * @return string
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
     * @param string $command
     *
     * @return \Robo\Result
     */
    protected function executeCommand($command)
    {
        $process = new Process($command);
        $process->setTimeout(null);
        if ($this->workingDirectory) {
            $process->setWorkingDirectory($this->workingDirectory);
        }
        $this->getExecTimer()->start();
        if ($this->isPrinted) {
            $process->run(function ($type, $buffer) {
                print $buffer;
            });
        } else {
            $process->run();
        }
        $this->getExecTimer()->stop();

        return new Result($this, $process->getExitCode(), $process->getOutput(), ['time' => $this->getExecTimer()->elapsed()]);
    }
}
