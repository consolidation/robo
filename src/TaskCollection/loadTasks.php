<?php
namespace Robo\TaskCollection;


trait loadTasks
{
    /**
     * @param $dirs
     * @return CleanDir
     */
    protected function taskCollection()
    {
        return new Collection();
    }
}
