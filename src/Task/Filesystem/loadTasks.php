<?php
namespace Robo\Task\Filesystem;

use Robo\Collection\Temporary;

trait loadTasks
{
    /**
     * @param $dirs
     * @return CleanDir
     */
    protected function taskCleanDir($dirs)
    {
        return $this->task(CleanDir::class, $dirs);
    }

    /**
     * @param $dirs
     * @return DeleteDir
     */
    protected function taskDeleteDir($dirs)
    {
        return $this->task(DeleteDir::class, $dirs);
    }

    /**
     * @param $prefix
     * @param $base
     * @param $includeRandomPart
     * @return WorkDir
     */
    protected function taskTmpDir($prefix = 'tmp', $base = '', $includeRandomPart = true)
    {
        return $this->task(TmpDir::class, $prefix, $base, $includeRandomPart);
    }

    /**
     * @param $finalDestination
     * @return TmpDir
     */
    protected function taskWorkDir($finalDestination)
    {
        return $this->task(WorkDir::class, $finalDestination);
    }

    /**
     * @param $dirs
     * @return CopyDir
     */
    protected function taskCopyDir($dirs)
    {
        return $this->task(CopyDir::class, $dirs);
    }

    /**
     * @param $dirs
     * @return MirrorDir
     */
    protected function taskMirrorDir($dirs)
    {
        return $this->task(MirrorDir::class, $dirs);
    }

    /**
     * @param $dirs
     * @return FlattenDir
     */
    protected function taskFlattenDir($dirs)
    {
        return $this->task(FlattenDir::class, $dirs);
    }

    /**
     * @return FilesystemStack
     */
    protected function taskFilesystemStack()
    {
        return $this->task(FilesystemStack::class);
    }
}
