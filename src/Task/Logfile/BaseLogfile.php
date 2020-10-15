<?php

namespace Robo\Task\Logfile;

use Robo\Task\BaseTask;
use Symfony\Component\Filesystem\Filesystem;

abstract class BaseLogfile extends BaseTask
{
    /**
     * @var string|string[]
     */
    protected $logfiles = [];

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * @param string|string[] $logfiles
     */
    public function __construct($logfiles)
    {
        is_array($logfiles)
            ? $this->logfiles = $logfiles
            : $this->logfiles[] = $logfiles;

        $this->filesystem = new Filesystem();
    }
}
