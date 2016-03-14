<?php
namespace Robo\Task\File;

use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        'taskConcat',
        'taskReplaceInFile',
        'taskWriteToFile',
        'taskTmpFile',
    ];

    public function register()
    {
        $this->getContainer()->add('taskConcat', Concat::class);
        $this->getContainer()->add('taskReplaceInFile', Replace::class);
        $this->getContainer()->add('taskWriteToFile', Write::class);
        $this->getContainer()->add('taskTmpFile', TmpFile::class);
    }
}
