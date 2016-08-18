<?php
namespace Robo\Task\Docker;

trait loadTasks
{
    protected function taskDockerRun($image)
    {
        return $this->task(Run::class, $image);
    }
    protected function taskDockerPull($image)
    {
        return $this->task(Pull::class, $image);
    }
    protected function taskDockerBuild($path = '.')
    {
        return $this->task(Build::class, $path);
    }
    protected function taskDockerStop($cidOrResult)
    {
        return $this->task(Stop::class, $cidOrResult);
    }
    protected function taskDockerCommit($cidOrResult)
    {
        return $this->task(Commit::class, $cidOrResult);
    }
    protected function taskDockerStart($cidOrResult)
    {
        return $this->task(Start::class, $cidOrResult);
    }
    protected function taskDockerRemove($cidOrResult)
    {
        return $this->task(Remove::class, $cidOrResult);
    }

    protected function taskDockerExec($cidOrResult)
    {
        return $this->task(Exec::class, $cidOrResult);
    }
}
