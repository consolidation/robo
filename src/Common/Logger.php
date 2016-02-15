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
        $taskName = TaskInfo::formatTaskName($task);
        $time = $result->getExecutionTime();
        $message = $result->getMessage();

        $printOutput = true;
        if ($task instanceof PrintedInterface) {
            $printOutput = !$task->getPrinted();
        }
        if ($printOutput) {
            $this->error("[$taskName] $message");
        }
        $this->error('[{name}] Exit code {code}', ['name' => $taskName, 'code' => $result->getExitCode()]);
        if ($time) {
            $this->notice('Time {time}', ['time' => $time]);
        }
    }

    /**
     * Log the result of a Robo task that was successful.
     */
    protected function logSuccessResult(Result $result)
    {
        $task = $result->getTask();
        $taskName = TaskInfo::formatTaskName($task);
        $time = $result->getExecutionTime();
        if ($time) {
            $this->success('[{name}] Done in {time}', ['name' => $taskName, 'time' => $time]);
        }
    }
}
