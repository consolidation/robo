<?php
namespace Robo\Common;

use Robo\Config;
use Robo\TaskInfo;

/**
 * Task input/output methods.  TaskIO is 'used' in BaseTask, so any
 * task that extends this class has access to all of the methods here.
 * printTaskInfo, printTaskSuccess, and printTaskError are the three
 * primary output methods that tasks are encouraged to use.  Tasks should
 * avoid using the IO trait output methods.
 */
trait TaskIO
{
    /**
     * Print information about a task in progress.
     */
    protected function printTaskInfo($text, $context = null)
    {
        // The 'note' style is used for both 'notice' and 'info' log levels;
        // However, 'notice' is printed at VERBOSITY_NORMAL, whereas 'info'
        // is only printed at VERBOSITY_VERBOSE.
        Config::logger()->notice($text, $this->getTaskContext($context));
    }

    /**
     * Provide notification that some part of the task succeeded.
     */
    protected function printTaskSuccess($text, $context = null)
    {
        Config::logger()->success($text, $this->getTaskContext($context));
    }

    /**
     * Provide notification that some operation in the task failed,
     * and the task cannot continue.
     */
    protected function printTaskError($text, $context = null)
    {
        Config::logger()->error($text, $this->getTaskContext($context));
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
     * inserted at the head of log messages by
     * Robo\Common\LogStyler::formatMessage().
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
