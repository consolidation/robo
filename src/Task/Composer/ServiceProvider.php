<?php
namespace Robo\Task\Composer;

use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        'taskComposerInstall',
        'taskComposerUpdate',
        'taskComposerDumpAutoload',
    ];

    public function register()
    {
        $this->getContainer()->add('taskComposerInstall', Install::class);
        $this->getContainer()->add('taskComposerUpdate', Update::class);
        $this->getContainer()->add('taskComposerDumpAutoload', DumpAutoload::class);
    }
}
