<?php
namespace Robo\Task\FileSystem;

use Robo\Container\SimpleServiceProvider;

class ServiceProvider extends SimpleServiceProvider
{
    public function __construct()
    {
        parent::__construct(
            [
                'taskCleanDir' => CleanDir::class,
                'taskDeleteDir' => DeleteDir::class,
                'taskTmpDir' => TmpDir::class,
                'taskCopyDir' => CopyDir::class,
                'taskMirrorDir' => MirrorDir::class,
                'taskFlattenDir' => FlattenDir::class,
                'taskFilesystemStack' => FilesystemStack::class,
            ]
        );
    }
}
