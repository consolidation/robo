<?php
namespace Robo\Task\Npm;

use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        'taskNpmInstall',
        'taskNpmUpdate',
    ];

    public function register()
    {
        $this->getContainer()->add('taskNpmInstall', Install::class);
        $this->getContainer()->add('taskNpmUpdate', Update::class);
    }
}
