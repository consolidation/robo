<?php
namespace Robo\Task\Base;

use Robo\Container\SimpleServiceProvider;

class ServiceProvider extends SimpleServiceProvider
{
    public function __construct()
    {
        parent::__construct(
            [
                'taskExec' => Exec::class,
                'taskExecStack' => ExecStack::class,
                'taskParallelExec' => ParallelExec::class,
                'taskSymfonyCommand' => SymfonyCommand::class,
                'taskWatch' => Watch::class,
            ]
        );
    }
}
