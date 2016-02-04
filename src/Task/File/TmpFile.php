<?php

namespace Robo\Task\File;

use Robo\Collection\Collection;
use Robo\Contract\TemporaryInterface;
use Robo\Collection\Temporary;

/**
 * Create a temporary directory that is automatically cleaned up
 * once the task collection is is part of completes.
 *
 * Use setTemporary(false) to make the directory persist after
 * completion, but still be deleted on rollback.
 *
 * Note that the path to the temporary file is available immediately
 * via the getPath() method, even though the directory is not
 * created until the task's run() method is executed..
 *
 * ``` php
 * <?php
 * $tmpFilePath = $this->taskTmpFile()
 *      ->line('-----')
 *      ->line(date('Y-m-d').' '.$title)
 *      ->line('----')
 *      ->addToCollection($collection)
 *      ->getPath();
 * ?>
 * ```
 */
class TmpFile extends Write implements TemporaryInterface
{
    use Temporary;

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
    private static function randomString($length = 12)
    {
        return substr(str_shuffle('23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ'), 0, $length);
    }

    /**
     * Delete our directory when requested to clean up our temporary objects.
     */
    public function cleanupTemporaries()
    {
        unlink($this->getPath());
    }
}
