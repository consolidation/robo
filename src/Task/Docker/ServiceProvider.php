<?php
namespace Robo\Task\Docker;

use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        'taskDockerRun',
        'taskDockerPull',
        'taskDockerBuild',
        'taskDockerStop',
        'taskDockerCommit',
        'taskDockerStart',
        'taskDockerRemove',
    ];

    public function register()
    {
        $this->getContainer()->add('taskDockerRun', Run::class);
        $this->getContainer()->add('taskDockerPull', Pull::class);
        $this->getContainer()->add('taskDockerBuild', Build::class);
        $this->getContainer()->add('taskDockerStop', Stop::class);
        $this->getContainer()->add('taskDockerCommit', Commit::class);
        $this->getContainer()->add('taskDockerStart', Start::class);
        $this->getContainer()->add('taskDockerRemove', Remove::class);
    }
}
