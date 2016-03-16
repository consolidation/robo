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
        return $this->task(__FUNCTION__, $filename);
    }

    /**
     * @param $filename
     *
     * @return Extract
     */
    protected function taskExtract($filename)
    {
        return $this->task(__FUNCTION__, $filename);
    }
}
