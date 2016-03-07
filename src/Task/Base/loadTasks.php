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
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Base\Exec',
            [$command]
        );
    }

    protected function taskExecStack()
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Base\ExecStack',
            []
        );
    }

    /**
     * @return ParallelExec
     */
    protected function taskParallelExec()
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Base\ParallelExec',
            []
        );
    }

    /**
     * @param $command
     * @return SymfonyCommand
     */
    protected function taskSymfonyCommand($command)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Base\SymfonyCommand',
            [$command]
        );
    }

    /**
     * @return Watch
     */
    protected function taskWatch()
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Base\Watch',
            [$this]
        );
    }
}
