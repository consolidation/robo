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

    /**
     * @param Process $process
     * @return type
     */
    public function __construct(Process $process)
    {
        $this->process = $process;
    }

    public static function create($container, $process)
    {
        $processExecutor = new self($process);

        $processExecutor->setLogger($container->get('logger'));
        $processExecutor->setProgressIndicator($container->get('progressIndicator'));
        $processExecutor->setConfig($container->get('config'));
        $processExecutor->setOutputAdapter($container->get('outputAdapter'));

        return $processExecutor;
    }

    /**
     * @return string
     */
    protected function getCommandDescription()
    {
        return $this->process->getCommandLine();
    }

    public function run()
    {
        return $this->execute($this->process);
    }
}
