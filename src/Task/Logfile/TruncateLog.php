<?php

namespace Robo\Task\Logfile;

use Robo\Common\ResourceExistenceChecker;
use Robo\Result;

/**
 * Truncates a log (or any other) file
 *
 * ``` php
 * <?php
 * $this->taskTruncateLog('logfile.log')->run();
 * // or use shortcut
 * $this->_truncateLog('logfile.log');
 *
 * ?>
 * ```
 */
class TruncateLog extends BaseLogfile
{
    use ResourceExistenceChecker;

    /**
     * {@inheritdoc}
     */
    public function run(): Result
    {
        if (!$this->checkResources($this->logfiles, 'file')) {
            return Result::error($this, 'Source files are missing!');
        }
        foreach ($this->logfiles as $logfile) {
            $this->filesystem->dumpFile($logfile, '');
            $this->printTaskInfo("Truncated {logfile}", ['logfile' => $logfile]);
        }

        return Result::success($this);
    }
}
