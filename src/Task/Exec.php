<?php
namespace Robo\Task;

use Robo\Output;
use Robo\Result;
use Robo\Task\Shared\CommandInterface;
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

    protected function taskExecStack($command)
    {
        return new ExecStackTask($command);
    }
}

/**
 * Executes shell script. Closes it when running in background mode.
 * Initial code from https://github.com/tiger-seo/PhpBuiltinServer by tiger-seo
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
    use \Robo\Output;
    use Shared\CommandInjected;

    protected $command;
    protected $background = false;
    protected $resource;
    protected $pipes = [];
    protected $isPrinted = false;

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
        return $this->command;
    }

    public function background()
    {
        $this->background = true;
        return $this;
    }

    public function arg($arg)
    {
        return $this->args($arg);
    }

    public function args($args)
    {
        if (!is_array($args)) {
            $args = func_get_args();
        }
        $this->command .= " ".implode(' ', $args);
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
        $this->printTaskInfo("running <info>{$this->command}</info>");
        $this->process = new Process($this->command);

        if (!$this->background and $this->isPrinted) {
            $this->process->run();
            return new Result($this, $this->process->getExitCode(), $this->process->getOutput());
        }

        if (!$this->background and !$this->isPrinted) {
            $this->process->run(function ($type, $buffer) {
                Process::ERR === $type ? print('ERR> '.$buffer) : print('OUT> '.$buffer);
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
class ExecStackTask implements TaskInterface
{
    use Shared\DynamicConfig;
    use Output;
    protected $exec = [];
    protected $result;
    protected $stopOnFail = false;

    public function run()
    {
        foreach ($this->exec as $command) {
            $this->result = (new ExecTask($command))->run();
            if (!$this->result->wasSuccessful() and $this->stopOnFail) {
                return $this->result;
            }
        }
        return Result::success($this);
    }
}