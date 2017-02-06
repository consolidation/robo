<?php

namespace Robo\Common;

use Robo\Contract\OutputAdapterInterface;
use Robo\Contract\OutputAwareInterface;
use Robo\Contract\TaskInterface;
use Robo\Contract\VerbosityThresholdInterface;
use Robo\Robo;
use Robo\Task\BaseTask;

class ProcessExecutor implements OutputAwareInterface
{
    use ExecTrait;
    use TaskIO; // uses LoggerAwareTrait and ConfigAwareTrait
    use ProgressIndicatorAwareTrait;
    use OutputAwareTrait;

    public function __construct($process)
    {
        $this->process = $process;
        $this->logger = Robo::logger();
        $this->setOutputAdapter(new OutputAdapter());
        $this->outputAdapter()->setOutput(Robo::output());
        $this->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_NORMAL);
    }

    public function getCommand()
    {
        return $this->process->getCommandLine();
    }

    public function run()
    {
        return $this->execute($this->process);
    }
}
