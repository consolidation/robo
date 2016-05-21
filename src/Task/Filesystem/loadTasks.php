<?php
namespace Robo\Task\Filesystem;

use Robo\Collection\Temporary;
use Robo\Container\SimpleServiceProvider;

trait loadTasks
{
    /**
     * Return services.
     */
    public static function getFilesystemServices()
    {
        return new SimpleServiceProvider(
            [
                'taskCleanDir' => CleanDir::class,
                'taskDeleteDir' => DeleteDir::class,
                'taskTmpDir' => TmpDir::class,
                'taskCopyDir' => CopyDir::class,
                'taskMirrorDir' => MirrorDir::class,
                'taskFlattenDir' => FlattenDir::class,
                'taskFilesystemStack' => FilesystemStack::class,
            ]
        );
    }

    /**
     * @param $dirs
     * @return CleanDir
     */
    protected function taskCleanDir($dirs)
    {
        return $this->task(__FUNCTION__, $dirs);
    }

    /**
     * @param $dirs
     * @return DeleteDir
     */
    protected function taskDeleteDir($dirs)
    {
        return $this->task(__FUNCTION__, $dirs);
    }

    /**
     * @param $prefix
     * @param $base
     * @param $includeRandomPart
     * @return TmpDir
     */
    protected function taskTmpDir($prefix = 'tmp', $base = '', $includeRandomPart = true)
    {
        return $this->task(__FUNCTION__, $prefix, $base, $includeRandomPart);
    }

    /**
     * @param $dirs
     * @return CopyDir
     */
    protected function taskCopyDir($dirs)
    {
        return $this->task(__FUNCTION__, $dirs);
    }

    /**
     * @param $dirs
     * @return MirrorDir
     */
    protected function taskMirrorDir($dirs)
    {
        return $this->task(__FUNCTION__, $dirs);
    }

    /**
     * @param $dirs
     * @return FlattenDir
     */
    protected function taskFlattenDir($dirs)
    {
        return $this->task(__FUNCTION__, $dirs);
    }

    /**
     * @return FilesystemStack
     */
    protected function taskFilesystemStack()
    {
        return $this->task(__FUNCTION__);
    }
}
