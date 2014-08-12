<?php
namespace Robo\Task;

use Robo\Output;
use Robo\Result;
use Robo\Task\Shared\CommandInterface;
use Robo\Task\Shared\CommandInjected;
use Robo\Task\Shared\CommandStack;
use Robo\Task\Shared\Executable;
use Robo\Task\Shared\TaskInterface;
use Symfony\Component\Process\Process;

/**
 * Task to execute shell scripts with `exec` command. Can be executed in background
 */
trait Exec  {
    private $runningCommands = [];
    protected function taskExec($command)
    {
        $exec = new ExecTask($command);
        $this->runningCommands[] = $exec;
        return $exec;
    }

    protected function taskExecStack()
    {
        return new ExecStackTask();
    }
}

/**
 * Executes shell script. Closes it when running in background mode.
 *
 * ``` php
 * <?php
 * $this->taskExec('compass')->arg()->run();
 *
 * $this->taskExec('compass watch')->background()->run();
 *
 * if ($this->taskExec('phpunit .')->run()->wasSuccessful()) {
 *  $this->say('tests passed');
 * }
 * ?>
 * ```
 */
class ExecTask implements TaskInterface, CommandInterface{
    use Output;
    use CommandInjected;
    use Executable;

    protected $command;
    protected $background = false;
    protected $timeout = null;
    protected $idleTimeout = null;

    /**
     * @var Process
     */
    protected $process;

    public function __construct($command)
    {
        $this->command = $this->retrieveCommand($command);
    }

    public function getCommand()
    {
        return trim($this->command. ' '.$this->arguments);
    }

    /**
     * Executes command in background mode (asynchronously)
     *
     * @return $this
     */
    public function background()
    {
        $this->background = true;
        return $this;
    }

    /**
     * Stop command if it runs longer then $timeout in seconds
     *
     * @param $timeout
     * @return $this
     */
    public function timeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * Stops command if it does not output something for a while
     *
     * @param $timeout
     * @return $this
     */
    public function idleTimeout($timeout)
    {
        $this->idleTimeout = $timeout;
        return $this;
    }

    public function __destruct()
    {
        $this->stop();
    }

    public function stop()
    {
        if ($this->background && $this->process->isRunning()) {
            $this->process->stop();
            $this->printTaskInfo("stopped <info>{$this->command}</info>");
        }        
    }

    public function run()
    {
        $command = $this->getCommand();
        $this->printTaskInfo("running <info>{$command}</info>");
        $this->process = new Process($command);
        $this->process->setTimeout($this->timeout);
        $this->process->setIdleTimeout($this->idleTimeout);
        $this->process->setWorkingDirectory($this->workingDirectory);


        if (!$this->background and !$this->isPrinted) {
            $this->process->run();
            return new Result($this, $this->process->getExitCode(), $this->process->getOutput());
        }

        if (!$this->background and $this->isPrinted) {
            $this->process->run(function ($type, $buffer) {
                Process::ERR === $type ? print('ER» '.$buffer) : print('» '.$buffer);
            });
            return new Result($this, $this->process->getExitCode(), $this->process->getOutput());
        }

        try {
            $this->process->start();
        } catch (\Exception $e) {
            return Result::error($this, $e->getMessage());
        }
        return Result::success($this);
    }
}

/**
 * Execute commands one by one in stack.
 * Stack can be stopped on first fail if you call `stopOnFail()`.
 *
 * ```php
 * <?php
 * $this->taskExecStack()
 *  ->stopOnFail()
 *  ->exec('mkdir site')
 *  ->exec('cd site')
 *  ->run();
 *
 * ?>
 * ```
 *
 * @method \Robo\Task\ExecStackTask exec(string)
 * @method \Robo\Task\ExecStackTask stopOnFail(string)
 */
class ExecStackTask extends CommandStack
{
}
