<?php
namespace Robo\Task\FileSystem;

use Robo\Collection\Temporary;

trait loadTasks
{
    /**
     * @param $dirs
     * @return CleanDir
     */
    protected function taskCleanDir($dirs)
    {
        return $this->task('CleanDir', $dirs);
    }

    /**
     * @param $dirs
     * @return DeleteDir
     */
    protected function taskDeleteDir($dirs)
    {
        return $this->task('DeleteDir', $dirs);
    }

    /**
     * @param $prefix
     * @param $base
     * @param $includeRandomPart
     * @return TmpDir
     */
    protected function taskTmpDir($prefix = 'tmp', $base = '', $includeRandomPart = true)
    {
        return $this->task('TmpDir', $prefix, $base, $includeRandomPart);
    }

    /**
     * @param $dirs
     * @return CopyDir
     */
    protected function taskCopyDir($dirs)
    {
        return $this->task('CopyDir', $dirs);
    }

    /**
     * @param $dirs
     * @return MirrorDir
     */
    protected function taskMirrorDir($dirs)
    {
        return $this->task('MirrorDir', $dirs);
    }

    /**
     * @param $dirs
     * @return FlattenDir
     */
    protected function taskFlattenDir($dirs)
    {
        return $this->task('FlattenDir', $dirs);
    }

    /**
     * @return FilesystemStack
     */
    protected function taskFilesystemStack()
    {
        return $this->task('FilesystemStack');
    }
}
