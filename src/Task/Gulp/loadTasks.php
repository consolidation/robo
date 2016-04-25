<?php
namespace Robo\Task\Gulp;

use Robo\Container\SimpleServiceProvider;

trait loadTasks
{
    /**
     * Return services.
     */
    public static function getGulpServices()
    {
        return new SimpleServiceProvider(
            [
                'taskGulpRun' => Run::class,
            ]
        );
    }

    /**
     * @param $task
     * @param null $pathToGulp
     * @return Run
     */
    protected function taskGulpRun($task = 'default', $pathToGulp = null)
    {
        return $this->task(__FUNCTION__, $task, $pathToGulp);
    }
}
