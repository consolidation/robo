<?php
namespace Robo\Task\Base;

use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        'taskExec',
        'taskExecStack',
        'taskParallelExec',
        'taskSymfonyCommand',
        'taskWatch',
    ];

    public function register()
    {
        $this->getContainer()->add('taskExec', Exec::class);
        $this->getContainer()->add('taskExecStack', ExecStack::class);
        $this->getContainer()->add('taskParallelExec', ParallelExec::class);
        $this->getContainer()->add('taskSymfonyCommand', SymfonyCommand::class);
        $this->getContainer()->add('taskWatch', Watch::class);
    }
}
