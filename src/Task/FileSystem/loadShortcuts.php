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
        return $this->getContainer()->get('taskCopyDir', [[$src => $dst]])->run();
    }

    /**
     * @param $src
     * @param $dst
     * @return \Robo\Result
     */
    protected function _mirrorDir($src, $dst)
    {
        return $this->getContainer()->get('taskMirrorDir', [[$src => $dst]])->run();
    }

    /**
     * @param $dir
     * @return \Robo\Result
     */
    protected function _deleteDir($dir)
    {
        return $this->getContainer()->get('taskDeleteDir', [$dir])->run();
    }

    /**
     * @param $dir
     * @return \Robo\Result
     */
    protected function _cleanDir($dir)
    {
        return $this->getContainer()->get('taskCleanDir', [$dir])->run();
    }

    /**
     * @param $from
     * @param $to
     * @return \Robo\Result
     */
    protected function _rename($from, $to)
    {
        return $this->getContainer()->get('taskFileSystemStack')->rename($from, $to)->run();
    }

    /**
     * @param $dir
     * @return \Robo\Result
     */
    protected function _mkdir($dir)
    {
        return $this->getContainer()->get('taskFileSystemStack')->mkdir($dir)->run();
    }

    /**
     * @param $dir
     * @return string|empty
     */
    protected function _tmpDir($prefix = 'tmp', $base = '', $includeRandomPart = true)
    {
        $result = $this->getContainer()->get('taskTmpDir', [$prefix, $base, $includeRandomPart])->run();
        $data = $result->getData() + ['path' => ''];
        return $data['path'];
    }

    /**
     * @param $file
     * @return \Robo\Result
     */
    protected function _touch($file)
    {
        return $this->getContainer()->get('taskFileSystemStack')->touch($file)->run();
    }

    /**
     * @param $file
     * @return \Robo\Result
     */
    protected function _remove($file)
    {
        return $this->getContainer()->get('taskFileSystemStack')->remove($file)->run();
    }

    /**
     * @param $file
     * @param $group
     * @return \Robo\Result
     */
    protected function _chgrp($file, $group)
    {
        return $this->getContainer()->get('taskFileSystemStack')->chgrp($file, $group)->run();
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
        return $this->getContainer()->get('taskFileSystemStack')->chmod($file, $permissions, $umask, $recursive)->run();
    }

    /**
     * @param $from
     * @param $to
     * @return \Robo\Result
     */
    protected function _symlink($from, $to)
    {
        return $this->getContainer()->get('taskFileSystemStack')->symlink($from, $to)->run();
    }

    /**
     * @param $from
     * @param $to
     * @return \Robo\Result
     */
    protected function _copy($from, $to)
    {
        return $this->getContainer()->get('taskFileSystemStack')->copy($from, $to)->run();
    }

    /**
     * @param $from
     * @param $to
     * @return \Robo\Result
     */
    protected function _flattenDir($from, $to)
    {
        return $this->getContainer()->get('taskFlattenDir', [[$from => $to]])->run();
    }
}
