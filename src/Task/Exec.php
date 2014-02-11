<?php
namespace Robo\Task;

use Robo\Result;

trait Exec  {
    private $runningCommands = [];
    protected function taskExec($command)
    {
        $exec = new ExecTask($command);
        $this->runningCommands[] = $exec;
        return $exec;
    }
}

/**
 * Initial code from https://github.com/tiger-seo/PhpBuiltinServer by tiger-seo
 *
 * Class Exec
 * @package Robo\Task
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

    public function args($arg)
    {
        if (!is_array($arg)) {
            $arg = func_get_args();
        }
        $this->command .= " ".implode(' ', $arg);
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
            system($this->command, $code);
            return $code;
        }

        if (!$this->background and !$this->isPrinted) {
            exec($this->command, $output, $code);
            return $code;
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