<?php
namespace Robo\Task\Shared;
use Robo\Result;

trait Process {

    function executeCommand($command)
    {
        $process = new \Symfony\Component\Process\Process($command);
        $process->setTimeout(null);
        $process->run();
		return new Result($this, $process->getExitCode(), $process->getOutput());
    }

} 