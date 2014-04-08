<?php
namespace Robo\Task;

use Robo\Output;
use Robo\Result;

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
class ExecTask implements TaskInterface {
    use \Robo\Output;

    protected $command;
    protected $background = false;
    protected $resource;
    protected $pipes = [];
    protected $isPrinted = false;

    public function __construct($command)
    {
        $this->command = $command;
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
        if ($this->background && $this->resource !== null) {
            foreach ($this->pipes AS $pipe) {
                fclose($pipe);
            }
            proc_terminate($this->resource, 2);
            unset($this->resource);
            $this->printTaskInfo("stopped <info>{$this->command}</info>");
        }        
    }
    
    public function run()
    {
        $this->printTaskInfo("running <info>{$this->command}</info>");
        if (!$this->background and $this->isPrinted) {
            $line = system($this->command, $code);
            return new Result($this, $code, $line);
        }

        if (!$this->background and !$this->isPrinted) {
            $line = exec($this->command, $output, $code);
            return new Result($this, $code, $line);
        }

        $descriptor = [
            ['pipe', 'r'],
            ['pipe', 'r'],
            ['pipe', 'r']
        ];
        $this->resource = proc_open($this->command, $descriptor, $this->pipes, null, null, ['bypass_shell' => true]);
        if (!is_resource($this->resource)) {
            return Result::error($this, 'Failed to run command.');
        }
        if (!proc_get_status($this->resource)['running']) {
            proc_close($this->resource);
            return Result::error($this, 'Failed to run command.');
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
    use DynamicConfig;
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
