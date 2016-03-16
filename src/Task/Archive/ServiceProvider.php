<?php
namespace Robo\Task\Archive;

use Robo\Container\SimpleServiceProvider;

class ServiceProvider extends SimpleServiceProvider
{
    public function __construct()
    {
        parent::__construct(
            [
                'taskExtract' => Extract::class,
                'taskPack' => Pack::class,
            ]
        );
    }
}
