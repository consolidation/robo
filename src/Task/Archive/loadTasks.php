<?php
namespace Robo\Task\Archive;

trait loadTasks
{
    /**
     * @param $filename
     * @return Archive
     */
    protected function taskArchive($filename)
    {
        return new Archive($filename);
    }

    /**
     * @param $filename
     * @return Extract
     */
    protected function taskExtract($filename)
    {
        return new Extract($filename);
    }
}
