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
        return $this->task('Concat', $files);
    }

    /**
     * @param $file
     * @return Replace
     */
    protected function taskReplaceInFile($file)
    {
        return $this->task('ReplaceInFile', $file);
    }

    /**
     * @param $file
     * @return Write
     */
    protected function taskWriteToFile($file)
    {
        return $this->task('WriteToFile', $file);
    }

    /**
     * @param $prefix
     * @param $base
     * @param $includeRandomPart
     * @return TmpFile
     */
    protected function taskTmpFile($filename = 'tmp', $extension = '', $baseDir = '', $includeRandomPart = true)
    {
        return $this->task('TmpFile', $filename, $extension, $baseDir, $includeRandomPart);
    }
}
