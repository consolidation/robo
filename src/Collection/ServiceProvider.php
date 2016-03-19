<?php
namespace Robo\Collection;

use Robo\Container\SimpleServiceProvider;

class ServiceProvider extends SimpleServiceProvider
{
    public function __construct()
    {
        parent::__construct(
            [
                'collection' => Collection::class,
                'completionWrapper' => CompletionWrapper::class,
            ]
        );
    }
}
