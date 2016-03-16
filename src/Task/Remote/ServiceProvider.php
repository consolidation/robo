<?php
namespace Robo\Task\Remote;

use Robo\Container\SimpleServiceProvider;

class ServiceProvider extends SimpleServiceProvider
{
    public function __construct()
    {
        parent::__construct(
            [
                'taskRsync' => Rsync::class,
                'taskSshExec' => Ssh::class,
            ]
        );
    }
}
