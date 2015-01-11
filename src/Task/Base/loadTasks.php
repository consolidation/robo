<?php
namespace Robo\Task\Base;

use Robo\Task\Base\ExecStack;

trait loadTasks
{
    /**
     * @param $files
     * @return Concat
     */
    protected function taskConcat($files)
    {
        return new Concat($files);
    }

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
     * @return ParallelExecTask
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