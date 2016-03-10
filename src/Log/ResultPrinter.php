<?php
namespace Robo\Log;

use Robo\Result;
use Robo\TaskInfo;
use Robo\Contract\PrintedInterface;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Robo's default logger
 */
class ResultPrinter implements LoggerAwareInterface
{
   use LoggerAwareTrait;

    /**
     * Log the result of a Robo task.
     */
    public function printResult(Result $result)
    {
        if (!$result->wasSuccessful()) {
            $this->printError($result);
        } else {
            $this->printSuccess($result);
        }
    }

    /**
     * Log that we are about to abort due to an error being encountered
     * in 'stop on fail' mode.
     */
    public function printStopOnFail($result)
    {
        $this->logger->notice('Stopping on fail. Exiting....');
        $this->logger->error('Exit Code: {code}', ['code' => $result->getExitCode()]);
    }

    /**
     * Log the result of a Robo task that returned an error.
     */
    protected function printError(Result $result)
    {
        $task = $result->getTask();
        $context = $result->getContext() + ['timer-label' => 'Time'];

        $printOutput = true;
        if ($task instanceof PrintedInterface) {
            $printOutput = !$task->getPrinted();
        }
        if ($printOutput) {
            $this->logger->error("{message}", $context);
        }
        $this->logger->error('Exit code {code}', $context);
    }

    /**
     * Log the result of a Robo task that was successful.
     */
    protected function printSuccess(Result $result)
    {
        $task = $result->getTask();
        $context = $result->getContext() + ['timer-label' => 'in'];
        $time = $result->getExecutionTime();
        if ($time) {
            $this->logger->success('Done', $context);
        }
    }
}
