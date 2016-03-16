<?php
namespace Robo\Task\Docker;

use Robo\Container\SimpleServiceProvider;

class ServiceProvider extends SimpleServiceProvider
{
    public function __construct()
    {
        parent::__construct(
            [
                'taskDockerRun' => Run::class,
                'taskDockerPull' => Pull::class,
                'taskDockerBuild' => Build::class,
                'taskDockerStop' => Stop::class,
                'taskDockerCommit' => Commit::class,
                'taskDockerStart' => Start::class,
                'taskDockerRemove' => Remove::class,
            ]
        );
    }
}
