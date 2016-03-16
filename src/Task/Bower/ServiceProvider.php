<?php
namespace Robo\Task\Bower;

use Robo\Container\SimpleServiceProvider;

class ServiceProvider extends SimpleServiceProvider
{
    public function __construct()
    {
        parent::__construct(
            [
                'taskBowerInstall' => Install::class,
                'taskBowerUpdate' => Update::class,
            ]
        );
    }
}
