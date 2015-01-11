<?php
namespace Robo\Task\FileSystem;

use Robo\Output;
use Robo\Contract\TaskInterface;
use Symfony\Component\Filesystem\Filesystem as sfFileSystem;

abstract class BaseDir implements TaskInterface
{
    use Output;

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