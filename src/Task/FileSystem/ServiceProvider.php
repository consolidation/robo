<?php
namespace Robo\Task\FileSystem;

use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        'taskCleanDir',
        'taskDeleteDir',
        'taskTmpDir',
        'taskCopyDir',
        'taskMirrorDir',
        'taskFlattenDir',
        'taskFileSystemStack',
    ];

    public function register()
    {
        $this->getContainer()->add('taskCleanDir', CleanDir::class);
        $this->getContainer()->add('taskDeleteDir', DeleteDir::class);
        $this->getContainer()->add('taskTmpDir', TmpDir::class);
        $this->getContainer()->add('taskCopyDir', CopyDir::class);
        $this->getContainer()->add('taskMirrorDir', MirrorDir::class);
        $this->getContainer()->add('taskFlattenDir', FlattenDir::class);
        $this->getContainer()->add('taskFileSystemStack', FilesystemStack::class);
    }
}
