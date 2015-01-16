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
        return new Exec($command);
    }

    protected function taskExecStack()
    {
        return new ExecStack();
    }

    /**
     * @return ParallelExec
     */
    protected function taskParallelExec()
    {
        return new ParallelExec();
    }

    /**
     * @param $command
     * @return SymfonyCommand
     */
    protected function taskSymfonyCommand($command)
    {
        return new SymfonyCommand($command);
    }

    /**
     * @return Watch
     */
    protected function taskWatch()
    {
        return new Watch($this);
    }
}