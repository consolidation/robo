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
     * @return Replace
     */
    protected function taskReplaceInFile($file)
    {
        return new Replace($file);
    }

    /**
     * @param $file
     * @return Write
     */
    protected function taskWriteToFile($file)
    {
        return new Write($file);
    }
} 
