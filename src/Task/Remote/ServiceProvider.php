<?php
namespace Robo\Task\Remote;

use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        'taskRsync',
        'taskSshExec',
    ];

    public function register()
    {
        $this->getContainer()->add('taskRsync', Rsync::class);
        $this->getContainer()->add('taskSshExec', Ssh::class);
    }
}
