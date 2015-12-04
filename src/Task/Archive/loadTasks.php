<?php
namespace Robo\Task\Archive;

trait loadTasks
{
    /**
     * @param $files
     * @return Concat
     */
    protected function taskExtract($filename)
    {
        return new Extract($filename);
    }
}
