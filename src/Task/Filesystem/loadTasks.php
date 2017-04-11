<?php
namespace Robo\Task\Filesystem;

trait loadTasks
{
    /**
     * @param string|string[] $dirs
     *
     * @return \Robo\Task\Filesystem\CleanDir
     */
    protected function taskCleanDir($dirs)
    {
        return $this->task(CleanDir::class, $dirs);
    }

    /**
     * @param string|string[] $dirs
     *
     * @return \Robo\Task\Filesystem\DeleteDir
     */
    protected function taskDeleteDir($dirs)
    {
        return $this->task(DeleteDir::class, $dirs);
    }

    /**
     * @param string $prefix
     * @param string $base
     * @param bool $includeRandomPart
     *
     * @return \Robo\Task\Filesystem\WorkDir
     */
    protected function taskTmpDir($prefix = 'tmp', $base = '', $includeRandomPart = true)
    {
        return $this->task(TmpDir::class, $prefix, $base, $includeRandomPart);
    }

    /**
     * @param string $finalDestination
     *
     * @return \Robo\Task\Filesystem\TmpDir
     */
    protected function taskWorkDir($finalDestination)
    {
        return $this->task(WorkDir::class, $finalDestination);
    }

    /**
     * @param string|string[] $dirs
     *
     * @return \Robo\Task\Filesystem\CopyDir
     */
    protected function taskCopyDir($dirs)
    {
        return $this->task(CopyDir::class, $dirs);
    }

    /**
     * @param string|string[] $dirs
     *
     * @return \Robo\Task\Filesystem\MirrorDir
     */
    protected function taskMirrorDir($dirs)
    {
        return $this->task(MirrorDir::class, $dirs);
    }

    /**
     * @param string|string[] $dirs
     *
     * @return \Robo\Task\Filesystem\FlattenDir
     */
    protected function taskFlattenDir($dirs)
    {
        return $this->task(FlattenDir::class, $dirs);
    }

    /**
     * @return \Robo\Task\Filesystem\FilesystemStack
     */
    protected function taskFilesystemStack()
    {
        return $this->task(FilesystemStack::class);
    }
}
