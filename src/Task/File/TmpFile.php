<?php

namespace Robo\Task\File;

use Robo\Collection\Collection;
use Robo\Contract\CompletionInterface;

/**
 * Create a temporary file that is automatically cleaned up
 * once the task collection is is part of completes. When created,
 * it is given a random filename.
 *
 * This temporary file may be manipulated exacatly like taskWrite().
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
class TmpFile extends Write implements CompletionInterface
{
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
     * Delete this file when our collection completes.
     * If this temporary file is not part of a collection,
     * then it will be deleted when the program terminates,
     * presuming that it was created by taskTmpFile() or _tmpFile().
     */
    public function complete()
    {
        unlink($this->getPath());
    }
}
