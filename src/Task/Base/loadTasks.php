<?php
namespace Robo\Task\Base;

use Robo\Container\SimpleServiceProvider;

trait loadTasks
{
    /**
     * Return services.
     */
    public static function getBaseServices()
    {
        return new SimpleServiceProvider(
            [
                'taskExec' => Exec::class,
                'taskExecStack' => ExecStack::class,
                'taskParallelExec' => ParallelExec::class,
                'taskSymfonyCommand' => SymfonyCommand::class,
                'taskWatch' => Watch::class,
            ]
        );
    }

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
