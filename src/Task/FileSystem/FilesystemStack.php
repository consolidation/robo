<?php
namespace Robo\Task\FileSystem;

use Robo\Result;
use Robo\Task\StackBasedTask;
use Symfony\Component\Filesystem\Filesystem as sfFileSystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Wrapper for [Symfony FileSystem](http://symfony.com/doc/current/components/filesystem.html) Component.
 * Comands are executed in stack and can be stopped on first fail with `stopOnFail` option.
 *
 * ``` php
 * <?php
 * $this->taskFileSystemStack()
 *      ->mkdir('logs')
 *      ->touch('logs/.gitignore')
 *      ->chgrp('www', 'www-data')
 *      ->symlink('/var/log/nginx/error.log', 'logs/error.log')
 *      ->run();
 *
 * // one line
 * $this->_touch('.gitignore');
 * $this->_mkdir('logs');
 *
 * ?>
 * ```
 *
 * @method mkdir($dir)
 * @method touch($file)
 * @method copy($from, $to, $force = null)
 * @method chmod($file, $permissions, $umask = null, $recursive = null)
 * @method remove($file)
 * @method rename($from, $to)
 * @method symlink($from, $to)
 * @method mirror($from, $to)
 * @method chgrp($file, $group)
 * @method chown($file, $user)
 */
class FilesystemStack extends StackBasedTask
{
    protected $fs;

    /**
     * Historically, FilesystemStack defaults to
     * stopOnFail(false), but StackBasedTask defaults
     * to stopOnFail(true).
     */
    public function __construct()
    {
        $this->fs = new sfFileSystem();
        $this->stopOnFail(false);
    }

    protected function getDelegate()
    {
        return $this->fs;
    }

    protected function _copy($from, $to, $force = false)
    {
        $this->fs->copy($from, $to, $force);
    }

    protected function _chmod($file, $permissions, $umask = 0000, $recursive = false)
    {
        $this->fs->chmod($file, $permissions, $umask, $recursive);
    }

    protected function _rename($origin, $target, $overwrite = false)
    {
        // we check that target does not exist
        if ((!$overwrite && is_readable($target)) || (file_exists($target) && !is_writable($target))) {
            throw new IOException(sprintf('Cannot rename because the target "%s" already exists.', $target), 0, null, $target);
        }

        // Due to a bug (limitation) in PHP, cross-volume renames do not work.
        // See: https://bugs.php.net/bug.php?id=54097
        if (true !== @rename($origin, $target)) {
            return $this->crossVolumeRename($origin, $target);
        }
        return true;
    }

    /**
     * Execute one task method
     */
    protected function callTaskMethod($command, $action)
    {
        try {
            $function_result = call_user_func_array($command, $action);
            return $this->processResult($function_result);
        } catch (IOExceptionInterface $e) {
            $this->printTaskInfo("<error>" . $e->getMessage() . "</error>");
            return Result::error($this, $e->getMessage(), $e->getPath());
        }
    }

    protected function crossVolumeRename($origin, $target)
    {
        // First step is to try to get rid of the target. If there
        // is a single, deletable file, then we will just unlink it.
        if (is_file($target)) {
            unlink($target);
        }
        // If the target still exists, we will try to delete it.
        // TODO: Note that if this fails partway through, then we cannot
        // adequately rollback.  Perhaps we need to preflight the operation
        // and determine if everything inside of $target is writable.
        if (file_exists($target)) {
            $deleteResult = (new DeleteDir($target))->run();
            if (!$deleteResult->wasSuccessful()) {
                return $deleteResult;
            }
        }
        $result = (new CopyDir([$origin => $target]))->run();
        if (!$result->wasSuccessful()) {
            return $result;
        }
        return (new DeleteDir($origin))->run();
    }
}
