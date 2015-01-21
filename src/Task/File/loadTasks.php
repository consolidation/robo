<?php 
namespace Robo\Task\File;

trait loadTasks
{
    /**
     * @param $files
     * @return Concat
     */
    protected function taskConcat($files)
    {
        return new Concat($files);
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
        return new Write($file);
    }
} 