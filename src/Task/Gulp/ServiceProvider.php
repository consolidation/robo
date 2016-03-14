<?php
namespace Robo\Task\Gulp;

use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        'taskGulpRun',
    ];

    public function register()
    {
        $this->getContainer()->add('taskGulpRun', Run::class);
    }
}
