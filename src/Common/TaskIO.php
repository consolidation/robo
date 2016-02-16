<?php
namespace Robo\Common;

use Robo\Config;
use Robo\TaskInfo;

trait TaskIO
{
    use IO;

    protected function printTaskInfo($text, $task = null)
    {
        // The 'note' style is used for both 'notice' and 'info' log levels;
        // However, 'notice' is printed at VERBOSITY_NORMAL, whereas 'info'
        // is only printed at VERBOSITY_VERBOSE.
        Config::logger()->notice($text, $this->getTaskContext($task));
    }

    protected function printTaskSuccess($text, $task = null)
    {
        Config::logger()->success($text, $this->getTaskContext($task));
    }

    protected function printTaskError($text, $task = null)
    {
        Config::logger()->error($text, $this->getTaskContext($task));
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

    protected function getTaskContext($task = null)
    {
        if (!$task) {
            $task = $this;
        }
        return TaskInfo::getTaskContext($task);
    }
}
