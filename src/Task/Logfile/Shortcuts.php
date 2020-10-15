<?php

namespace Robo\Task\Logfile;

use Robo\Result;

trait Shortcuts
{
    /**
     * @param string $logfile
     *
     * @return \Robo\Result
     */
    protected function _rotateLog(string $logfile): Result
    {
        return $this->taskRotateLog($logfile)->run();
    }

    /**
     * @param string|array $logfile
     *
     * @return \Robo\Result
     */
    protected function _truncateLog($logfile): Result
    {
        return $this->taskTruncateLog($logfile)->run();
    }
}
