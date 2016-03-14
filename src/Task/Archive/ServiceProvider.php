<?php
namespace Robo\Task\Archive;

use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        'taskExtract',
        'taskPack',
    ];

    public function register()
    {
        $this->getContainer()->add('taskExtract', Extract::class);
        $this->getContainer()->add('taskPack', Pack::class);
    }
}
