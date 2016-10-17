<?php
namespace Robo\Task\Docker;

trait loadTasks
{
    /**
     * @param string $image
     *
     * @return \Robo\Task\Docker\Run
     */
    protected function taskDockerRun($image)
    {
        return $this->task(Run::class, $image);
    }

    /**
     * @param string $image
     *
     * @return \Robo\Task\Docker\Pull
     */
    protected function taskDockerPull($image)
    {
        return $this->task(Pull::class, $image);
    }

    /**
     * @param string $path
     *
     * @return \Robo\Task\Docker\Build
     */
    protected function taskDockerBuild($path = '.')
    {
        return $this->task(Build::class, $path);
    }

    /**
     * @param string|\Robo\Task\Docker\Result $cidOrResult
     *
     * @return \Robo\Task\Docker\Stop
     */
    protected function taskDockerStop($cidOrResult)
    {
        return $this->task(Stop::class, $cidOrResult);
    }

    /**
     * @param string|\Robo\Task\Docker\Result $cidOrResult
     *
     * @return \Robo\Task\Docker\Commit
     */
    protected function taskDockerCommit($cidOrResult)
    {
        return $this->task(Commit::class, $cidOrResult);
    }

    /**
     * @param string|\Robo\Task\Docker\Result $cidOrResult
     *
     * @return \Robo\Task\Docker\Start
     */
    protected function taskDockerStart($cidOrResult)
    {
        return $this->task(Start::class, $cidOrResult);
    }

    /**
     * @param string|\Robo\Task\Docker\Result $cidOrResult
     *
     * @return \Robo\Task\Docker\Remove
     */
    protected function taskDockerRemove($cidOrResult)
    {
        return $this->task(Remove::class, $cidOrResult);
    }

    /**
     * @param string|\Robo\Task\Docker\Result $cidOrResult
     *
     * @return \Robo\Task\Docker\Exec
     */
    protected function taskDockerExec($cidOrResult)
    {
        return $this->task(Exec::class, $cidOrResult);
    }
}
