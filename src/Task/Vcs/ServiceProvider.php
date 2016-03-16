<?php
namespace Robo\Task\Vcs;

use Robo\Container\SimpleServiceProvider;

class ServiceProvider extends SimpleServiceProvider
{
    public function __construct()
    {
        parent::__construct(
            [
                'taskSvnStack' => SvnStack::class,
                'taskGitStack' => GitStack::class,
            ]
        );
    }
}
