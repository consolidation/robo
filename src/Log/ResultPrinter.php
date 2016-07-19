<?php
namespace Robo\Log;

use Robo\Result;
use Robo\TaskInfo;
use Robo\Contract\PrintedInterface;
use Robo\Contract\ProgressIndicatorAwareInterface;
use Robo\Common\ProgressIndicatorAwareTrait;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Log the creation of Result objects.
 */
class ResultPrinter implements LoggerAwareInterface, ProgressIndicatorAwareInterface
{
    use LoggerAwareTrait;
    use ProgressIndicatorAwareTrait;

    /**
     * Log the result of a Robo task.
     *
     * Returns 'true' if the message is printed, or false if it isn't.
     *
     * @return boolean
     */
    public function printResult(Result $result)
    {
        if (!$result->wasSuccessful()) {
            return $this->printError($result);
        } else {
            return $this->printSuccess($result);
        }
    }

    /**
     * Log that we are about to abort due to an error being encountered
     * in 'stop on fail' mode.
     */
    public function printStopOnFail($result)
    {
        $this->hideProgressIndicator();
        $this->logger->notice('Stopping on fail. Exiting....');
        $this->logger->error('Exit Code: {code}', ['code' => $result->getExitCode()]);
    }

    /**
     * Log the result of a Robo task that returned an error.
     */
    protected function printError(Result $result)
    {
        $this->hideProgressIndicator();
        $task = $result->getTask();
        $context = $result->getContext() + ['timer-label' => 'Time', '_style' => []];
        $context['_style']['message'] = '';

        $printOutput = true;
        if ($task instanceof PrintedInterface) {
            $printOutput = !$task->getPrinted();
        }
        if ($printOutput) {
            $this->logger->error("{message}", $context);
        }
        $this->logger->error('Exit code {code}', $context);
        return true;
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
        return false;
    }
}
