<?php
namespace Robo\Task\Gulp;


trait loadTasks
{
    /**
     * @param $task
     * @param null $pathToGulp
     * @return Run
     */
    protected function taskGulpRun($task='default',$pathToGulp = null) {
        return new Run($task,$pathToGulp);
    }
} 