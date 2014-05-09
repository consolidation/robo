<?php
namespace Robo\Task\Shared;
use Robo\Result;
use \Symfony\Component\Process\Process as SymfonyProcess;

trait Executable {

    protected $arguments;

    function executeCommand($command)
    {
        $process = new SymfonyProcess($command);
        $process->setTimeout(null);
        $process->run(function ($type, $buffer) {
            SymfonyProcess::ERR === $type ? print('ER» '.$buffer) : print('» '.$buffer);
        });

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