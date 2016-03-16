<?php
namespace Robo\Collection;

use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        'collection',
        'completionWrapper',
    ];

    public function register()
    {
        $this->getContainer()->add('collection', Collection::class);
        $this->getContainer()->add('completionWrapper', CompletionWrapper::class);
    }
}
