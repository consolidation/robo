<?php
namespace Robo\Collection;

use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        'collection',
    ];

    public function register()
    {
        $this->getContainer()->add('collection', Collection::class);
    }
}
