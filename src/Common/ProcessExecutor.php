<?php

namespace Robo\Common;

use Psr\Log\LoggerAwareInterface;
use Robo\Contract\ConfigAwareInterface;
use Robo\Contract\OutputAwareInterface;
use Robo\Contract\VerbosityThresholdInterface;
use Symfony\Component\Process\Process;

class ProcessExecutor implements ConfigAwareInterface, LoggerAwareInterface, OutputAwareInterface, VerbosityThresholdInterface
{
    use ExecTrait;
    use TaskIO; // uses LoggerAwareTrait and ConfigAwareTrait
    use ProgressIndicatorAwareTrait;
    use OutputAwareTrait;

    public function __construct(Process $process)
    {
        $this->process = $process;
        // $this->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_NORMAL);
    }

    /**
     * @return string
     */
    public function getCommand()
    {
        return $this->process->getCommandLine();
    }

    public function run()
    {
        return $this->execute($this->process);
    }
}
