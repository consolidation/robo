<?php
namespace Robo\Task\Base;

trait loadTasks
{
    /**
     * @param string|\Robo\Contract\CommandInterface $command
     *
     * @return Exec
     */
    protected function taskExec($command)
    {
        return $this->task(Exec::class, $command);
    }

    /**
     * @return ExecStack
     */
    protected function taskExecStack()
    {
        return $this->task(ExecStack::class);
    }

    /**
     * @return ParallelExec
     */
    protected function taskParallelExec()
    {
        return $this->task(ParallelExec::class);
    }

    /**
     * @param $command
     * @return SymfonyCommand
     */
    protected function taskSymfonyCommand($command)
    {
        return $this->task(SymfonyCommand::class, $command);
    }

    /**
     * @return Watch
     */
    protected function taskWatch()
    {
        return $this->task(Watch::class, $this);
    }
}
