<?php
namespace Robo\Task\ApiGen;

use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        'taskApiGen',
    ];

    public function register()
    {
        $this->getContainer()->add('taskApiGen', ApiGen::class);
    }
}
