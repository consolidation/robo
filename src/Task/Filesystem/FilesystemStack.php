<?php
namespace Robo\Task\Filesystem;

use Robo\Result;
use Robo\Task\StackBasedTask;
use Symfony\Component\Filesystem\Filesystem as sfFilesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Robo\Contract\BuilderAwareInterface;
use Robo\Common\BuilderAwareTrait;

/**
 * Wrapper for [Symfony Filesystem](http://symfony.com/doc/current/components/filesystem.html) Component.
 * Comands are executed in stack and can be stopped on first fail with `stopOnFail` option.
 *
 * ``` php
 * <?php
 * $this->taskFilesystemStack()
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
 * @method $this mkdir($dir)
 * @method $this touch($file)
 * @method $this copy($from, $to, $force = null)
 * @method $this chmod($file, $permissions, $umask = null, $recursive = null)
 * @method $this chgrp($file, $group, $recursive = null)
 * @method $this chown($file, $user, $recursive = null)
 * @method $this remove($file)
 * @method $this rename($from, $to)
 * @method $this symlink($from, $to)
 * @method $this mirror($from, $to)
 */
class FilesystemStack extends StackBasedTask implements BuilderAwareInterface
{
    use BuilderAwareTrait;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $fs;

    public function __construct()
    {
        $this->fs = new sfFilesystem();
    }

    /**
     * @return \Symfony\Component\Filesystem\Filesystem
     */
    protected function getDelegate()
    {
        return $this->fs;
    }

    /**
     * @param string $from
     * @param string $to
     * @param bool $force
     */
    protected function _copy($from, $to, $force = false)
    {
        $this->fs->copy($from, $to, $force);
    }

    /**
     * @param string|string[]|\Traversable $file
     * @param int $permissions
     * @param int $umask
     * @param bool $recursive
     */
    protected function _chmod($file, $permissions, $umask = 0000, $recursive = false)
    {
        $this->fs->chmod($file, $permissions, $umask, $recursive);
    }

    /**
     * @param string|string[]|\Traversable $file
     * @param string $group
     * @param bool $recursive
     */
    protected function _chgrp($file, $group, $recursive = null)
    {
        $this->fs->chgrp($file, $group, $recursive);
    }

    /**
     * @param string|string[]|\Traversable $file
     * @param string $user
     * @param bool $recursive
     */
    protected function _chown($file, $user, $recursive = null)
    {
        $this->fs->chown($file, $user, $recursive);
    }

    /**
     * @param string $origin
     * @param string $target
     * @param bool $overwrite
     *
     * @return null|true|\Robo\Result
     */
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
     * @param string $origin
     * @param string $target
     *
     * @return null|\Robo\Result
     */
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
            $this->fs->remove($target);
        }

        /** @var \Robo\Result $result */
        $result = $this->collectionBuilder()->taskCopyDir([$origin => $target])->run();
        if (!$result->wasSuccessful()) {
            return $result;
        }
        $this->fs->remove($origin);
    }
}
