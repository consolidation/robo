<?php
namespace Robo\Task;
use Robo\Task;
use Robo\TaskException;
use Robo\TaskInterface;

class Exec implements TaskInterface {

    protected $command;
    protected $isBackground = false;
    protected $resource;
    protected $pipes;

    public function __construct($command)
    {
        $this->command = $command;
    }

    public function background()
    {
        $this->isBackground = true;
        return $this;
    }

    public function args($arg)
    {
        if (!is_array($arg)) {
            $arg = func_get_args();
        }
        $this->command .= " ".implode(' ', $arg);
    }

    public function __destruct()
    {
        if ($this->isBackground && $this->resource !== null) {
            foreach ($this->pipes AS $pipe) {
                fclose($pipe);
            }
            proc_terminate($this->resource, 2);
            unset($this->resource);
        }
    }
    
    public function run()
    {
        if (!$this->isBackground) {
            return shell_exec($this->command);
        }

        $this->resource = proc_open($this->command, ['r'], $this->pipes, null, null, ['bypass_shell' => true]);
        if (!is_resource($this->resource)) {
            throw new TaskException($this, 'Failed to run command.');
        }
        if (!proc_get_status($this->resource)['running']) {
            proc_close($this->resource);
            throw new TaskException($this, 'Failed to run command.');
        }
    }

}
 