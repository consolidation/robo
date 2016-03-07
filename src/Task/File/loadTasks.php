<?php
namespace Robo\Task\File;

use Robo\Collection\Temporary;

trait loadTasks
{
    /**
     * @param $files
     * @return Concat
     */
    protected function taskConcat($files)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\File\Concat',
            [$files]
        );
    }

    /**
     * @param $file
     * @return Replace
     */
    protected function taskReplaceInFile($file)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\File\Replace',
            [$file]
        );
    }

    /**
     * @param $file
     * @return Write
     */
    protected function taskWriteToFile($file)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\File\Write',
            [$file]
        );
    }

    /**
     * @param $prefix
     * @param $base
     * @param $includeRandomPart
     * @return TmpFile
     */
    protected function taskTmpFile($filename = 'tmp', $extension = '', $baseDir = '', $includeRandomPart = true)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\File\TmpFile',
            [$filename, $extension, $baseDir, $includeRandomPart]
        );
    }
}
