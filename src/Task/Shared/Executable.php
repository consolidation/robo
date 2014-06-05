<?php
namespace Robo\Task\Shared;
use Robo\Result;
use \Symfony\Component\Process\Process as SymfonyProcess;

trait Executable {

    protected $arguments;
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


    protected function executeCommand($command)
    {
        $process = new SymfonyProcess($command);
        $process->setTimeout(null);
        if ($this->isPrinted) {
            $process->run(function ($type, $buffer) {
                SymfonyProcess::ERR === $type ? print('ER» '.$buffer) : print($buffer);
            });
        } else {
            $process->run();
        }

		return new Result($this, $process->getExitCode(), $process->getOutput());
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
        $this->arguments .= " ".implode(' ', $args);
        return $this;
    }

    public function option($option, $value = null)
    {
        if ($option !== null and strpos($option, '-') !== 0) {
            $option = "--$option";
        }
        $this->arguments .= null == $option ? '' : " " . $option;
        $this->arguments .= null == $value ? '' : " " . $value;
        return $this;
    }


} 