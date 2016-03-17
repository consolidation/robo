<?php
namespace Robo\Task\Docker;

use Robo\Container\SimpleServiceProvider;

trait loadTasks
{
    /**
     * Return services.
     */
    public static function getDockerServices()
    {
        return new SimpleServiceProvider(
            [
                'taskDockerRun' => Run::class,
                'taskDockerPull' => Pull::class,
                'taskDockerBuild' => Build::class,
                'taskDockerStop' => Stop::class,
                'taskDockerCommit' => Commit::class,
                'taskDockerStart' => Start::class,
                'taskDockerRemove' => Remove::class,
            ]
        );
    }

    protected function taskDockerRun($image)
    {
        return $this->task(__FUNCTION__, $image);
    }
    protected function taskDockerPull($image)
    {
        return $this->task(__FUNCTION__, $image);
    }
    protected function taskDockerBuild($path = '.')
    {
        return $this->task(__FUNCTION__, $path);
    }
    protected function taskDockerStop($cidOrResult)
    {
        return $this->task(__FUNCTION__, $cidOrResult);
    }
    protected function taskDockerCommit($cidOrResult)
    {
        return $this->task(__FUNCTION__, $cidOrResult);
    }
    protected function taskDockerStart($cidOrResult)
    {
        return $this->task(__FUNCTION__, $cidOrResult);
    }
    protected function taskDockerRemove($cidOrResult)
    {
        return $this->task(__FUNCTION__, $cidOrResult);
    }

    protected function taskDockerExec($cidOrResult)
    {
        return $this->task(__FUNCTION__, $cidOrResult);
    }
}
