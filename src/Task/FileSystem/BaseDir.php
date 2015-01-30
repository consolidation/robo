<?php
namespace Robo\Task\FileSystem;

use Robo\Task\BaseTask;
use Symfony\Component\Filesystem\Filesystem as sfFileSystem;

abstract class BaseDir extends BaseTask
{
    protected $dirs = [];

    protected $fs;

    public function __construct($dirs)
    {
        is_array($dirs)
            ? $this->dirs = $dirs
            : $this->dirs[] = $dirs;

        $this->fs = new sfFileSystem();
    }

}
