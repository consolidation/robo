<?php
namespace Robo\Common;

use Robo\Config;
use Robo\TaskInfo;

trait TaskIO
{
    use IO;

    protected function printTaskInfo($text, $context = null)
    {
        // The 'note' style is used for both 'notice' and 'info' log levels;
        // However, 'notice' is printed at VERBOSITY_NORMAL, whereas 'info'
        // is only printed at VERBOSITY_VERBOSE.
        Config::logger()->notice($text, $this->getTaskContext($context));
    }

    protected function printTaskSuccess($text, $context = null)
    {
        Config::logger()->success($text, $this->getTaskContext($context));
    }

    protected function printTaskError($text, $context = null)
    {
        Config::logger()->error($text, $this->getTaskContext($context));
    }

    protected function formatBytes($size, $precision = 2)
    {
        if ($size === 0) {
            return 0;
        }
        $base = log($size, 1024);
        $suffixes = array('', 'k', 'M', 'G', 'T');
        return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
    }

    protected function getPrintedTaskName($task = null)
    {
        if (!$task) {
            $task = $this;
        }
        return TaskInfo::formatTaskName($task);
    }

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
