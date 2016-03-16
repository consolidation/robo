<?php
namespace Robo\Task\Docker;

trait loadTasks
{
    protected function taskDockerRun($image)
    {
        return $this->task('DockerRun', $image);
    }
    protected function taskDockerPull($image)
    {
        return $this->task('DockerPull', $image);
    }
    protected function taskDockerBuild($path = '.')
    {
        return $this->task('DockerBuild', $path);
    }
    protected function taskDockerStop($cidOrResult)
    {
        return $this->task('DockerStop', $cidOrResult);
    }
    protected function taskDockerCommit($cidOrResult)
    {
        return $this->task('DockerCommit', $cidOrResult);
    }
    protected function taskDockerStart($cidOrResult)
    {
        return $this->task('DockerStart', $cidOrResult);
    }
    protected function taskDockerRemove($cidOrResult)
    {
        return $this->task('DockerRemove', $cidOrResult);
    }

    protected function taskDockerExec($cidOrResult)
    {
        return $this->task('DockerExec', $cidOrResult);
    }
}
