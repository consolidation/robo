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
        return $this->task('Exec', $command);
    }

    protected function taskExecStack()
    {
        return $this->task('ExecStack');
    }

    /**
     * @return ParallelExec
     */
    protected function taskParallelExec()
    {
        return $this->task('ParallelExec');
    }

    /**
     * @param $command
     * @return SymfonyCommand
     */
    protected function taskSymfonyCommand($command)
    {
        return $this->task('SymfonyCommand', $command);
    }

    /**
     * @return Watch
     */
    protected function taskWatch()
    {
        return $this->task('Watch', $this);
    }
}
