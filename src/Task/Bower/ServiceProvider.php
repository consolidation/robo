<?php
namespace Robo\Task\Bower;

use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        'taskBowerInstall',
        'taskBowerUpdate',
    ];

    public function register()
    {
        $this->getContainer()->add('taskBowerInstall', Install::class);
        $this->getContainer()->add('taskBowerUpdate', Update::class);
    }
}
