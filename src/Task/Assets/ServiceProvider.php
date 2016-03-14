<?php
namespace Robo\Task\Assets;

use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        'taskMinify',
        'taskImageMinify',
        'taskLess',
        'taskScss',
    ];

    public function register()
    {
        $this->getContainer()->add('taskMinify', Minify::class);
        $this->getContainer()->add('taskImageMinify', ImageMinify::class);
        $this->getContainer()->add('taskLess', Less::class);
        $this->getContainer()->add('taskScss', Scss::class);
    }
}
