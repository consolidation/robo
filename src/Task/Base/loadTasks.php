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
        return $this->task(Exec::class, $command);
    }

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
