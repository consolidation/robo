<?php
namespace Robo\Task\Gulp;

trait loadTasks
{
    /**
     * @param string $task
     * @param null|string $pathToGulp
     *
     * @return \Robo\Task\Gulp\Run|\Robo\Collection\CollectionBuilder
     */
    protected function taskGulpRun($task = 'default', $pathToGulp = null)
    {
        return $this->task(Run::class, $task, $pathToGulp);
    }
}
