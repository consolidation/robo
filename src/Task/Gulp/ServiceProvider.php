<?php
namespace Robo\Task\Gulp;

use Robo\Container\SimpleServiceProvider;

class ServiceProvider extends SimpleServiceProvider
{
    public function __construct()
    {
        parent::__construct(
            [
                'taskGulpRun' => Run::class,
            ]
        );
    }
}
