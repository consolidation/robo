<?php
namespace Robo\Task\Npm;

use Robo\Container\SimpleServiceProvider;

class ServiceProvider extends SimpleServiceProvider
{
    public function __construct()
    {
        parent::__construct(
            [
                'taskNpmInstall' => Install::class,
                'taskNpmUpdate' => Update::class,
            ]
        );
    }
}
