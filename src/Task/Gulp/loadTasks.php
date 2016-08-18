<?php
namespace Robo\Task\Gulp;

use Robo\Container\SimpleServiceProvider;

trait loadTasks
{
    /**
     * @param $task
     * @param null $pathToGulp
     * @return Run
     */
    protected function taskGulpRun($task = 'default', $pathToGulp = null)
    {
        return $this->task(Run::class, $task, $pathToGulp);
    }
}
