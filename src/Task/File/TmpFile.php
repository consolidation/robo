<?php
namespace Robo\Task\File;

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
 * $tmpFilePath = $this->taskTmpFile()
 *      ->line('-----')
 *      ->line(date('Y-m-d').' '.$title)
 *      ->line('----')
 *      ->collect($collection)
 *      ->getPath();
 * ?>
 * ```
 */
class TmpFile extends Write implements TransientInterface
{
    use Transient;

    public function __construct($filename = 'tmp', $extension = '', $baseDir = '', $includeRandomPart = true)
    {
        if (empty($base)) {
            $base = sys_get_temp_dir();
        }
        if ($includeRandomPart) {
            $random = static::randomString();
            $filename = "{$filename}_{$random}";
        }
        $filename .= $extension;
        parent::__construct("{$base}/{$filename}");
    }

    /**
     * Generate a suitably random string to use as the suffix for our
     * temporary file.
     */
    private static function randomString($length = 12) {
        return substr(str_shuffle("23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ"), 0, $length);
    }

    /**
     * Delete our directory when requested to clean up our transient objects.
     */
    public function cleanupTransients() {
        unlink($this->getPath());
    }
}
