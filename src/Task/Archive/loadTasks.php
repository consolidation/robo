<?php

namespace Robo\Task\Archive;

trait loadTasks
{
    /**
     * @param $filename
     *
     * @return Archive
     */
    protected function taskPack($filename)
    {
        return new Pack($filename);
    }

    /**
     * @param $filename
     *
     * @return Extract
     */
    protected function taskExtract($filename)
    {
        return new Extract($filename);
    }
}
