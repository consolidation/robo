<?php
namespace Robo\Task\FileSystem;

use Robo\Result;
use Robo\TaskCollection\Collection;
use Robo\Contract\CompletionInterface;
use Robo\Task\FileSystem\DeleteDir;

/**
 * Deletes dir
 *
 * ``` php
 * <?php
 * // Delete on rollback or on successful completion.
 * // Note that in this example, everything is deleted at
 * // the end of $collection->run().
 * $tmpPath = $this->taskTmpDir()->collect($collection)->getDir();
 * $this->taskFileSystemStack()
 *           ->mkdir("$tmpPath/log")
 *           ->touch("$tmpPath/log/error.txt")
 *           ->collect($collection);
 * $collection->run();
 * // as shortcut (deleted when program exits)
 * $result = $this->_tmpDir();
 * $data = $result->getData();
 * $tmpPath = $data['path'];
 * ?>
 * ```
 */
class TmpDir extends BaseDir implements CompletionInterface
{
    protected $base;
    protected $prefix;

    public function __construct($base = '', $prefix = 'tmp', $extension = '')
    {
        if (empty($base)) {
            $base = sys_get_temp_dir();
        }
        $random = static::randomString();
        parent::__construct([ "{$base}/{$prefix}_{$random}{$extension}" ]);
    }

    /**
     * Generate a suitably random string to use as the suffix for our
     * temporary directory.
     */
    private static function randomString($length = 12) {
        return substr(str_shuffle("23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ"), 0, $length);
    }

    /**
     * Create our temporary directory.
     */
    public function run()
    {
        foreach ($this->dirs as $dir) {
            $this->fs->mkdir($dir);
            $this->printTaskInfo("Created <info>$dir</info>...");
        }
        return Result::success($this, '', ['path' => $this->getDir()]);
    }

    /**
     * Run our completion tasks when done.
     */
    public function complete() {
        $completionTask = new DeleteDir($this->dirs);
        $completionTask->run();
    }

    /**
     * Get a reference to the path to the temporary directory, so that
     * it may be used to create other tasks.  Note that the directory
     * is not actually created until the task runs.
     */
    public function getDir() {
        return $this->dirs[0];
    }
}
