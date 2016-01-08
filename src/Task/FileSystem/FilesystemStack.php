<?php
namespace Robo\Task\FileSystem;

use Robo\Result;
use Robo\Task\WrapperTask;
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
 */
class FilesystemStack extends WrapperTask
{
    protected $fs;

    /**
     * Historically, FilesystemStack defaults to
     * stopOnFail(false), but WrapperTask defaults
     * to stopOnFail(true).
     */
    public function __construct() {
        $this->fs = new sfFileSystem();
        $this->stopOnFail(false);
    }

    protected function getDelegate() {
        return $this->fs;
    }

    public function _copy($from, $to, $force = false)
    {
        $this->fs->copy($from, $to, $force);
    }

    public function _chmod($file, $permissions, $umask = 0000, $recursive = false)
    {
        $this->fs->chmod($file, $permissions, $umask, $recursive);
    }

    /**
     * Execute one task method
     */
    protected function callTaskMethod($command, $action) {
        try {
            $function_result = call_user_func_array($command, $action);
            return $this->processResult($function_result);
        } catch (IOExceptionInterface $e) {
            $this->printTaskInfo("<error>" . $e->getMessage() . "</error>");
            return Result::error($this, $e->getMessage(), $e->getPath());
        }
    }

}
