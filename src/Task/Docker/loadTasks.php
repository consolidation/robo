<?php
namespace Robo\Task\Docker;

trait loadTasks
{
    protected function taskDockerRun($image)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Docker\Run',
            [$image]
        );
    }
    protected function taskDockerPull($image)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Docker\Pull',
            [$image]
        );
    }
    protected function taskDockerBuild($path = '.')
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Docker\Build',
            [$path]
        );
    }
    protected function taskDockerStop($cidOrResult)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Docker\Stop',
            [$cidOrResult]
        );
    }
    protected function taskDockerCommit($cidOrResult)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Docker\Commit',
            [$cidOrResult]
        );
    }
    protected function taskDockerStart($cidOrResult)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Docker\Start',
            [$cidOrResult]
        );
    }
    protected function taskDockerRemove($cidOrResult)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Docker\Remove',
            [$cidOrResult]
        );
    }

    protected function taskDockerExec($cidOrResult)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Docker\Exec',
            [$cidOrResult]
        );
    }
}
