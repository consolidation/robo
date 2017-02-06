<?php

namespace Robo\Common;

use Robo\Contract\OutputAwareInterface;
use Robo\Contract\VerbosityThresholdInterface;
use Robo\Robo;
use Symfony\Component\Process\Process;

class ProcessExecutor implements OutputAwareInterface
{
    use ExecTrait;
    use TaskIO; // uses LoggerAwareTrait and ConfigAwareTrait
    use ProgressIndicatorAwareTrait;
    use OutputAwareTrait;

    public function __construct(Process $process)
    {
        $this->process = $process;
        $this->logger = Robo::logger();
        $this->setOutputAdapter(new OutputAdapter());
        $this->outputAdapter()->setOutput(Robo::output());
        $this->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_NORMAL);
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
