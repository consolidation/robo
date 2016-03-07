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
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Archive\Pack',
            [$filename]
        );
    }

    /**
     * @param $filename
     *
     * @return Extract
     */
    protected function taskExtract($filename)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Archive\Extract',
            [$filename]
        );
    }
}
