<?php
namespace Robo\Task\Base;

trait loadTasks
{
    /**
     * @param $command
     * @return Exec
     */
    protected function taskExec($command)
    {
        return $this->task(__FUNCTION__, $command);
    }

    protected function taskExecStack()
    {
        return $this->task(__FUNCTION__);
    }

    /**
     * @return ParallelExec
     */
    protected function taskParallelExec()
    {
        return $this->task(__FUNCTION__);
    }

    /**
     * @param $command
     * @return SymfonyCommand
     */
    protected function taskSymfonyCommand($command)
    {
        return $this->task(__FUNCTION__, $command);
    }

    /**
     * @return Watch
     */
    protected function taskWatch()
    {
        return $this->task(__FUNCTION__, $this);
    }
}
