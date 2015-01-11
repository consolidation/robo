<?php
namespace Robo\Task;
use Robo\Output;
use Robo\Result;
use Robo\Task\FileSystem\CopyDir;
use Robo\Traits\DynamicConfig;
use Robo\Contract\TaskInterface;
use Robo\Util\FileSystem as UtilsFileSystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Contains useful tasks to work with filesystem.
 *
 */
trait FileSystem
{
    /**
     * @param $dirs
     * @return \Robo\Task\FileSystem\CleanDirTask
     */
    protected function taskCleanDir($dirs)
    {
        return new FileSystem\CleanDir($dirs);
    }

    /**
     * @param $dirs
     * @return \Robo\Task\FileSystem\DeleteDirTask
     */
    protected function taskDeleteDir($dirs)
    {
        return new FileSystem\DeleteDir($dirs);
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
     * @return \Robo\Task\FileSystem\MirrorDirTask
     */
    protected function taskMirrorDir($dirs)
    {
        return new FileSystem\MirrorDir($dirs);
    }

    protected function taskReplaceInFile($file)
    {
        return new FileSystem\ReplaceInFile($file);
    }

    protected function taskWriteToFile($file)
    {
        return new FileSystem\WriteToFile($file);
    }

    protected function taskRequire($file)
    {
        return new FileSystem\taskRequireFile($file);
    }

    protected function taskFileSystemStack()
    {
        return new FileSystem\Filesystem();
    }
}