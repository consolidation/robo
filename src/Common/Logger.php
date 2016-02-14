<?php
namespace Robo\Common;

use Robo\Contract\LogResultInterface;
use Robo\Result;
use Psr\Log\AbstractLogger;
use Robo\Common\TaskIO;
use Robo\Contract\PrintedInterface;

/**
 * Robo's default logger
 *
 * TODO: Make this a Psr\Log logger
 */
class Logger /* extends AbstractLogger */ implements LogResultInterface
{
    use TaskIO;

    /**
     * Log the result of a Robo task.
     */
    public function logResult(Result $result)
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
    public function logStopOnFail($result)
    {
        $this->printTaskError("Stopping on fail. Exiting....");
        $this->printTaskError("<error>Exit Code: {$result->getExitCode()}</error>");
    }

    protected function printError(Result $result)
    {
        $task = $result->getTask();
        $message = $result->getMessage();
        $lines = explode("\n", $message);

        $printOutput = true;

        $time = $result->getExecutionTime();
        if ($time) $time = "Time <fg=yellow>$time</fg=yellow>";

        if ($task instanceof PrintedInterface) {
            $printOutput = !$task->getPrinted();
        }
        if ($printOutput) {
            foreach ($lines as $msg) {
                if (!$msg) continue;
                $this->printTaskError($msg, $task);
            }
        }
        $this->printTaskError("<error> Exit code " . $result->getExitCode() . " </error> $time", $task);
    }

    protected function printSuccess(Result $result)
    {
        $task = $result->getTask();
        $time = $result->getExecutionTime();
        if (!$time) return;
        $time = "in <fg=yellow>$time</fg=yellow>";
        $this->printTaskSuccess("Done $time", $task);
    }
}
