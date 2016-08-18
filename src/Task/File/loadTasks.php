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
        return $this->task(Concat::class, $files);
    }

    /**
     * @param $file
     * @return Replace
     */
    protected function taskReplaceInFile($file)
    {
        return $this->task(Replace::class, $file);
    }

    /**
     * @param $file
     * @return Write
     */
    protected function taskWriteToFile($file)
    {
        return $this->task(Write::class, $file);
    }

    /**
     * @param $prefix
     * @param $base
     * @param $includeRandomPart
     * @return TmpFile
     */
    protected function taskTmpFile($filename = 'tmp', $extension = '', $baseDir = '', $includeRandomPart = true)
    {
        return $this->task(TmpFile::class, $filename, $extension, $baseDir, $includeRandomPart);
    }
}
