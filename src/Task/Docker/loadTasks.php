<?php 
namespace Robo\Task\Docker;

trait loadTasks
{
    protected function taskDockerRun($image)
    {
        return new Run($image);
    }
    protected function taskDockerPull($image)
    {
        return new Pull($image);
    }
    protected function taskDockerBuild($path = '.')
    {
        return new Build($path);
    }
    protected function taskDockerStop($cidOrResult)
    {
        return new Stop($cidOrResult);
    }
    protected function taskDockerCommit($cidOrResult)
    {
        return new Commit($cidOrResult);
    }
    protected function taskDockerStart($cidOrResult)
    {
        return new Start($cidOrResult);
    }
    protected function taskDockerRemove($cidOrResult)
    {
        return new Remove($cidOrResult);
    }

    protected function taskDockerExec($cidOrResult)
    {
        return new Exec($cidOrResult);
    }
}