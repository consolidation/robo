<?php
namespace Robo\Common;

use Robo\Config;
use Robo\TaskInfo;
use Consolidation\Log\ConsoleLogLevel;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * Task input/output methods.  TaskIO is 'used' in BaseTask, so any
 * task that extends this class has access to all of the methods here.
 * printTaskInfo, printTaskSuccess, and printTaskError are the three
 * primary output methods that tasks are encouraged to use.  Tasks should
 * avoid using the IO trait output methods.
 */
trait TaskIO
{
    use LoggerAwareTrait;

    public function logger()
    {
        return $this->logger ?: Config::logger();
    }

    /**
     * Print information about a task in progress.
     *
     * With the Symfony Console logger, NOTICE is displayed at VERBOSITY_VERBOSE
     * and INFO is displayed at VERBOSITY_VERY_VERBOSE.
     *
     * Robo overrides the default such that NOTICE is displayed at
     * VERBOSITY_NORMAL and INFO is displayed at VERBOSITY_VERBOSE.
     *
     * n.b. We should probably have printTaskNotice for our ordinary
     * output, and use printTaskInfo for less interesting messages.
     */
    protected function printTaskInfo($text, $context = null)
    {
        // The 'note' style is used for both 'notice' and 'info' log levels;
        // However, 'notice' is printed at VERBOSITY_NORMAL, whereas 'info'
        // is only printed at VERBOSITY_VERBOSE.
        $this->logger()->notice($text, $this->getTaskContext($context));
    }

    /**
     * Provide notification that some part of the task succeeded.
     *
     * With the Symfony Console logger, success messages are remapped to NOTICE,
     * and displayed in VERBOSITY_VERBOSE. When used with the Robo logger,
     * success messages are displayed at VERBOSITY_NORMAL.
     */
    protected function printTaskSuccess($text, $context = null)
    {
        // Not all loggers will recognize ConsoleLogLevel::SUCCESS.
        // We therefore log as LogLevel::NOTICE, and apply a '_level'
        // override in the context so that this message will be
        // logged as SUCCESS if that log level is recognized.
        $context['_level'] = ConsoleLogLevel::SUCCESS;
        $this->logger()->notice($text, $this->getTaskContext($context));
    }

    /**
     * Provide notification that there is something wrong, but
     * execution can continue.
     *
     * Warning messages are displayed at VERBOSITY_NORMAL.
     */
    protected function printTaskWarning($text, $context = null)
    {
        $this->logger()->warning($text, $this->getTaskContext($context));
    }

    /**
     * Provide notification that some operation in the task failed,
     * and the task cannot continue.
     *
     * Error messages are displayed at VERBOSITY_NORMAL.
     */
    protected function printTaskError($text, $context = null)
    {
        $this->logger()->error($text, $this->getTaskContext($context));
    }

    /**
     * Provide debugging notification.  These messages are only
     * displayed if the log level is VERBOSITY_DEBUG.
     */
    protected function printTaskDebug($text, $context = null)
    {
        $this->logger()->debug($text, $this->getTaskContext($context));
    }

    /**
     * Format a quantity of bytes.
     */
    protected function formatBytes($size, $precision = 2)
    {
        if ($size === 0) {
            return 0;
        }
        $base = log($size, 1024);
        $suffixes = array('', 'k', 'M', 'G', 'T');
        return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
    }

    /**
     * Get the formatted task name for use in task output.
     * This is placed in the task context under 'name', and
     * used as the log label by Robo\Common\RoboLogStyle,
     * which is inserted at the head of log messages by
     * Robo\Common\CustomLogStyle::formatMessage().
     *
     * @return string
     */
    protected function getPrintedTaskName($task = null)
    {
        if (!$task) {
            $task = $this;
        }
        return TaskInfo::formatTaskName($task);
    }

    /**
     * @return array with context information
     */
    protected function getTaskContext($context = null)
    {
        if (!$context) {
            $context = [];
        }
        if (!is_array($context)) {
            $context = ['task' => $context];
        }
        if (!array_key_exists('task', $context)) {
            $context['task'] = $this;
        }

        return $context + TaskInfo::getTaskContext($context['task']);
    }
}
