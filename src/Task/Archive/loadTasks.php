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
        return $this->task('Pack', $filename);
    }

    /**
     * @param $filename
     *
     * @return Extract
     */
    protected function taskExtract($filename)
    {
        return $this->task('Extract', $filename);
    }
}
