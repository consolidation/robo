<?php
namespace Robo\Task\Shared;
use Robo\Result;
use \Symfony\Component\Process\Process as SymfonyProcess;

trait Process {

    function executeCommand($command)
    {
        $process = new SymfonyProcess($command);
        $process->setTimeout(null);
        $process->run(function ($type, $buffer) {
            SymfonyProcess::ERR === $type ? print('ER» '.$buffer) : print('» '.$buffer);
        });

		return new Result($this, $process->getExitCode(), $process->getOutput());
    }

} 