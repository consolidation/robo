<?php
namespace Robo\Task\FileSystem;

trait loadTasks
{
    /**
     * @param $dirs
     * @return CleanDir
     */
    protected function taskCleanDir($dirs)
    {
        return new CleanDir($dirs);
    }

    /**
     * @param $dirs
     * @return DeleteDir
     */
    protected function taskDeleteDir($dirs)
    {
        return new DeleteDir($dirs);
    }

    /**
     * @param $dirs
     * @return CopyDir
     */
    protected function taskCopyDir($dirs)
    {
        return new CopyDir($dirs);
    }

    /**
     * @param $dirs
     * @return MirrorDir
     */
    protected function taskMirrorDir($dirs)
    {
        return new MirrorDir($dirs);
    }

    /**
     * @param $file
     * @return ReplaceInFile
     */
    protected function taskReplaceInFile($file)
    {
        return new ReplaceInFile($file);
    }

    /**
     * @param $file
     * @return WriteToFile
     */
    protected function taskWriteToFile($file)
    {
        return new WriteToFile($file);
    }

    /**
     * @return Filesystem
     */
    protected function taskFilesystemStack()
    {
        return new Filesystem();
    }
} 