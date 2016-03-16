<?php
namespace Robo\Task\File;

use Robo\Container\SimpleServiceProvider;

class ServiceProvider extends SimpleServiceProvider
{
    public function __construct()
    {
        parent::__construct(
            [
                'taskConcat' => Concat::class,
                'taskReplaceInFile' => Replace::class,
                'taskWriteToFile' => Write::class,
                'taskTmpFile' => TmpFile::class,
            ]
        );
    }
}
