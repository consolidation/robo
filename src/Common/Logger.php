<?php
namespace Robo\Common;

use Robo\Result;
use Robo\TaskInfo;
use Robo\Contract\PrintedInterface;
use Robo\Contract\LogResultInterface;

use Psr\Log\AbstractLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;

/**
 * Robo's default logger
 */
class Logger extends StyledConsoleLogger implements LogResultInterface
{
    public function __construct(OutputInterface $output, array $verbosityLevelMap = array(), array $formatLevelMap = array(), array $formatFunctionMap = array(), string $stylerClassname = null)
    {
        parent::__construct($output, [], [], [], '\Robo\Common\LogStyler');
    }

    /**
     * Log the result of a Robo task.
     */
    public function logResult(Result $result)
    {
        if (!$result->wasSuccessful()) {
            $this->logErrorResult($result);
        } else {
            $this->logSuccessResult($result);
        }
    }

    /**
     * Log that we are about to abort due to an error being encountered
     * in 'stop on fail' mode.
     */
    public function logStopOnFail($result)
    {
        $this->notice('Stopping on fail. Exiting....');
        $this->error('Exit Code: {code}', ['code' => $result->getExitCode()]);
    }

    /**
     * Log the result of a Robo task that returned an error.
     */
    protected function logErrorResult(Result $result)
    {
        $task = $result->getTask();
        $context = $result->getContext() + ['timer-label' => 'Time'];

        $printOutput = true;
        if ($task instanceof PrintedInterface) {
            $printOutput = !$task->getPrinted();
        }
        if ($printOutput) {
            $this->error("{message}", $context);
        }
        $this->error('Exit code {code}', $context);
    }

    /**
     * Log the result of a Robo task that was successful.
     */
    protected function logSuccessResult(Result $result)
    {
        $task = $result->getTask();
        $context = $result->getContext() + ['timer-label' => 'in'];
        $time = $result->getExecutionTime();
        if ($time) {
            $this->success('Done', $context);
        }
    }
}
