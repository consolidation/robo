<?php
namespace Robo\Task\Composer;

use Robo\Container\SimpleServiceProvider;

class ServiceProvider extends SimpleServiceProvider
{
    public function __construct()
    {
        parent::__construct(
            [
                'taskComposerInstall' => Install::class,
                'taskComposerUpdate' => Update::class,
                'taskComposerDumpAutoload' => DumpAutoload::class,
            ]
        );
    }
}
