<?php
namespace Robo\Task\FileSystem;

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
        return (new CopyDir([$src => $dst]))->run();
    }

    /**
     * @param $src
     * @param $dst
     * @return \Robo\Result
     */
    protected function _mirrorDir($src, $dst)
    {
        return (new MirrorDir([$src => $dst]))->run();
    }

    /**
     * @param $dir
     * @return \Robo\Result
     */
    protected function _deleteDir($dir)
    {
        return (new DeleteDir($dir))->run();
    }

    /**
     * @param $dir
     * @return \Robo\Result
     */
    protected function _cleanDir($dir)
    {
        return (new CleanDir($dir))->run();
    }

    /**
     * @param $from
     * @param $to
     * @return \Robo\Result
     */
    protected function _rename($from, $to)
    {
        return (new FilesystemStack)->rename($from, $to)->run();
    }

    /**
     * @param $dir
     * @return \Robo\Result
     */
    protected function _mkdir($dir)
    {
        return (new FilesystemStack)->mkdir($dir)->run();
    }

    /**
     * @param $dir
     * @return string|empty
     */
    protected function _tmpDir($prefix = 'tmp', $base = '', $includeRandomPart = true)
    {
        $result = Temporary::wrap(new TmpDir($prefix, $base, $includeRandomPart))->run();
        $data = $result->getData() + ['path' => ''];
        return $data['path'];
    }

    /**
     * @param $file
     * @return \Robo\Result
     */
    protected function _touch($file)
    {
        return (new FilesystemStack)->touch($file)->run();
    }

    /**
     * @param $file
     * @return \Robo\Result
     */
    protected function _remove($file)
    {
        return (new FilesystemStack)->remove($file)->run();
    }

    /**
     * @param $file
     * @param $group
     * @return \Robo\Result
     */
    protected function _chgrp($file, $group)
    {
        return (new FilesystemStack)->chgrp($file, $group)->run();
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
        return (new FilesystemStack)->chmod($file, $permissions, $umask, $recursive)->run();
    }

    /**
     * @param $from
     * @param $to
     * @return \Robo\Result
     */
    protected function _symlink($from, $to)
    {
        return (new FilesystemStack)->symlink($from, $to)->run();
    }

    /**
     * @param $from
     * @param $to
     * @return \Robo\Result
     */
    protected function _copy($from, $to)
    {
        return (new FilesystemStack)->copy($from, $to)->run();
    }

    /**
     * @param $from
     * @param $to
     * @return \Robo\Result
     */
    protected function _flattenDir($from, $to)
    {
        return (new FlattenDir([$from => $to]))->run();
    }
}
