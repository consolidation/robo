<?php

namespace Robo\Task\FileSystem;

use Robo\Result;
use Robo\Collection\Collection;
use Robo\Contract\CompletionInterface;

/**
 * Create a temporary directory that is automatically cleaned up
 * once the task collection is is part of completes.
 *
 * Move the directory to another location to prevent its deletion.
 *
 * ``` php
 * <?php
 * // Delete on rollback or on successful completion.
 * // Note that in this example, everything is deleted at
 * // the end of $collection->run().
 * $tmpPath = $this->taskTmpDir()->addToCollection($collection)->getPath();
 * $this->taskFileSystemStack()
 *           ->mkdir("$tmpPath/log")
 *           ->touch("$tmpPath/log/error.txt")
 *           ->addToCollection($collection);
 * $collection->run();
 * // as shortcut (deleted when program exits)
 * $tmpPath = $this->_tmpDir();
 * ?>
 * ```
 */
class TmpDir extends BaseDir implements CompletionInterface
{
    protected $base;
    protected $prefix;
    protected $cwd;
    protected $savedWorkingDirectory;

    public function __construct($prefix = 'tmp', $base = '', $includeRandomPart = true)
    {
        if (empty($base)) {
            $base = sys_get_temp_dir();
        }
        if ($includeRandomPart) {
            $random = static::randomString();
            $prefix = "{$prefix}_{$random}";
        }
        parent::__construct(["{$base}/{$prefix}"]);
    }

    /**
     * Generate a suitably random string to use as the suffix for our
     * temporary directory.
     */
    private static function randomString($length = 12)
    {
        return substr(str_shuffle('23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ'), 0, $length);
    }

    /**
     * Flag that we should cwd to the temporary directory when it is
     * created, and restore the old working directory when it is deleted.
     */
    public function cwd()
    {
        $this->cwd = true;

        return $this;
    }

    /**
     * Create our temporary directory.
     */
    public function run()
    {
        // Save the current working directory
        $this->savedWorkingDirectory = getcwd();
        foreach ($this->dirs as $dir) {
            $this->fs->mkdir($dir);
            $this->printTaskInfo("Created <info>$dir</info>...");

            // Change the current working directory, if requested
            if ($this->cwd) {
                chdir($dir);
            }
        }

        return Result::success($this, '', ['path' => $this->getPath()]);
    }

    /**
     * Delete this directory when our collection completes.
     * If this temporary directory is not part of a collection,
     * then it will be deleted when the program terminates,
     * presuming that it was created by taskTmpDir() or _tmpDir().
     */
    public function complete()
    {
        // Restore the current working directory, if we redirected it.
        if ($this->cwd) {
            chdir($this->savedWorkingDirectory);
        }
        (new DeleteDir($this->dirs))->run();
    }

    /**
     * Get a reference to the path to the temporary directory, so that
     * it may be used to create other tasks.  Note that the directory
     * is not actually created until the task runs.
     */
    public function getPath()
    {
        return $this->dirs[0];
    }
}
