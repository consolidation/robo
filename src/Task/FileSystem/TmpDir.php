<?php
namespace Robo\Task\FileSystem;

use Robo\Result;
use Robo\TaskCollection\Collection;
use Robo\Contract\TransientInterface;
use Robo\TaskCollection\Transient;
use Robo\Task\FileSystem\DeleteDir;

/**
 * Create a temporary directory that is automatically cleaned up
 * once the task collection is is part of completes.
 *
 * Use ->setTransient(false) to make the directory persist after
 * completion, but still be deleted on rollback.
 *
 * ``` php
 * <?php
 * // Delete on rollback or on successful completion.
 * // Note that in this example, everything is deleted at
 * // the end of $collection->run().
 * $tmpPath = $this->taskTmpDir()->collect($collection)->getPath();
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
class TmpDir extends BaseDir implements TransientInterface
{
    use Transient;

    protected $base;
    protected $prefix;

    public function __construct($prefix = 'tmp', $base = '', $includeRandomPart = true)
    {
        if (empty($base)) {
            $base = sys_get_temp_dir();
        }
        if ($includeRandomPart) {
            $random = static::randomString();
            $prefix = "{$prefix}_{$random}";
        }
        parent::__construct([ "{$base}/{$prefix}" ]);
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
        return Result::success($this, '', ['path' => $this->getPath()]);
    }

    /**
     * Delete our directory when requested to clean up our transient objects.
     */
    public function cleanupTransients() {
        (new DeleteDir($this->dirs))->run();
    }

    /**
     * Get a reference to the path to the temporary directory, so that
     * it may be used to create other tasks.  Note that the directory
     * is not actually created until the task runs.
     */
    public function getPath() {
        return $this->dirs[0];
    }
}
