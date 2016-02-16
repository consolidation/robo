<?php
namespace Robo\Common;

use Robo\Result;
use Robo\TaskInfo;
use Robo\Contract\PrintedInterface;
use Robo\Contract\LogResultInterface;
use Robo\Common\ConsoleLogLevel; // maybe: use Symfony\Component\Console\ConsoleLogLevel;

use Psr\Log\LogLevel;
use Psr\Log\AbstractLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;

/**
 * Robo's default logger
 */
class Logger extends StyledConsoleLogger implements LogResultInterface
{
    public function __construct(OutputInterface $output)
    {
        // In Robo, we use log level 'notice' for messages that appear all
        // the time, and 'info' for messages that appear only during verbose
        // output. We have no 'very verbose' (-vv) level. 'Debug' is -vvv, as usual.
        $roboVerbosityOverrides = [
            LogLevel::NOTICE => OutputInterface::VERBOSITY_NORMAL,
            LogLevel::INFO => OutputInterface::VERBOSITY_VERBOSE,
        ];
        // Robo should send all log messages to stderr. So should Symfony.
        // See: https://en.wikipedia.org/wiki/Standard_streams
        //   "Standard error was added to Unix after several wasted phototypesetting runs ended with error messages being typeset instead of displayed on the user's terminal."
        $roboFormatLevelOverrides = array(
            LogLevel::EMERGENCY => self::ERROR,
            LogLevel::ALERT => self::ERROR,
            LogLevel::CRITICAL => self::ERROR,
            LogLevel::ERROR => self::ERROR,
            LogLevel::WARNING => self::ERROR,
            LogLevel::NOTICE => self::ERROR,
            LogLevel::INFO => self::ERROR,
            LogLevel::DEBUG => self::ERROR,
            ConsoleLogLevel::OK => self::ERROR,
            ConsoleLogLevel::SUCCESS => self::ERROR,
        );
        parent::__construct($output, $roboVerbosityOverrides, $roboFormatLevelOverrides, [], '\Robo\Common\LogStyler');
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
