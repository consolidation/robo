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
        return $this->taskAssembler()->assemble(
            '\Robo\Task\FileSystem\CleanDir',
            [$dirs]
        );
    }

    /**
     * @param $dirs
     * @return DeleteDir
     */
    protected function taskDeleteDir($dirs)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\FileSystem\DeleteDir',
            [$dirs]
        );
    }

    /**
     * @param $prefix
     * @param $base
     * @param $includeRandomPart
     * @return TmpDir
     */
    protected function taskTmpDir($prefix = 'tmp', $base = '', $includeRandomPart = true)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\FileSystem\TmpDir',
            [$prefix, $base, $includeRandomPart]
        );
    }

    /**
     * @param $dirs
     * @return CopyDir
     */
    protected function taskCopyDir($dirs)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\FileSystem\CopyDir',
            [$dirs]
        );
    }

    /**
     * @param $dirs
     * @return MirrorDir
     */
    protected function taskMirrorDir($dirs)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\FileSystem\MirrorDir',
            [$dirs]
        );
    }

    /**
     * @param $dirs
     * @return FlattenDir
     */
    protected function taskFlattenDir($dirs)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\FileSystem\FlattenDir',
            [$dirs]
        );
    }

    /**
     * @return FilesystemStack
     */
    protected function taskFilesystemStack()
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\FileSystem\FilesystemStack'
        );
    }
}
