<?php
namespace Robo\Task\Filesystem;

use Robo\Collection\Temporary;

trait loadShortcuts
{
    /**
     * @param $src
     * @param $dst
     * @return \Robo\Result
     */
    protected function _copyDir($src, $dst)
    {
        return $this->taskCopyDir([$src => $dst])->run();
    }

    /**
     * @param $src
     * @param $dst
     * @return \Robo\Result
     */
    protected function _mirrorDir($src, $dst)
    {
        return $this->taskMirrorDir([$src => $dst])->run();
    }

    /**
     * @param $dir
     * @return \Robo\Result
     */
    protected function _deleteDir($dir)
    {
        return $this->taskDeleteDir($dir)->run();
    }

    /**
     * @param $dir
     * @return \Robo\Result
     */
    protected function _cleanDir($dir)
    {
        return $this->taskCleanDir($dir)->run();
    }

    /**
     * @param $from
     * @param $to
     * @return \Robo\Result
     */
    protected function _rename($from, $to)
    {
        return $this->taskFilesystemStack()->rename($from, $to)->run();
    }

    /**
     * @param $dir
     * @return \Robo\Result
     */
    protected function _mkdir($dir)
    {
        return $this->taskFilesystemStack()->mkdir($dir)->run();
    }

    /**
     * @param $dir
     * @return string|empty
     */
    protected function _tmpDir($prefix = 'tmp', $base = '', $includeRandomPart = true)
    {
        $result = $this->taskTmpDir($prefix, $base, $includeRandomPart)->run();
        return isset($result['path']) ? $result['path'] : '';
    }

    /**
     * @param $file
     * @return \Robo\Result
     */
    protected function _touch($file)
    {
        return $this->taskFilesystemStack()->touch($file)->run();
    }

    /**
     * @param $file
     * @return \Robo\Result
     */
    protected function _remove($file)
    {
        return $this->taskFilesystemStack()->remove($file)->run();
    }

    /**
     * @param $file
     * @param $group
     * @return \Robo\Result
     */
    protected function _chgrp($file, $group)
    {
        return $this->taskFilesystemStack()->chgrp($file, $group)->run();
    }

    /**
     * @param $file
     * @param $permissions
     * @param int $umask
     * @param bool $recursive
     * @return \Robo\Result
     */
    protected function _chmod($file, $permissions, $umask = 0000, $recursive = false)
    {
        return $this->taskFilesystemStack()->chmod($file, $permissions, $umask, $recursive)->run();
    }

    /**
     * @param $from
     * @param $to
     * @return \Robo\Result
     */
    protected function _symlink($from, $to)
    {
        return $this->taskFilesystemStack()->symlink($from, $to)->run();
    }

    /**
     * @param $from
     * @param $to
     * @return \Robo\Result
     */
    protected function _copy($from, $to)
    {
        return $this->taskFilesystemStack()->copy($from, $to)->run();
    }

    /**
     * @param $from
     * @param $to
     * @return \Robo\Result
     */
    protected function _flattenDir($from, $to)
    {
        return $this->taskFlattenDir([$from => $to])->run();
    }
}
