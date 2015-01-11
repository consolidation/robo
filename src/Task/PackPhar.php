<?php
namespace Robo\Task;
use Robo\Result;
use Robo\Task\Shared\TaskInterface;
use Symfony\Component\Console\Helper\ProgressHelper;

/**
 * Packs files into phar
 */
trait PackPhar {

    /**
     * @param $filename
     * @return \Robo\Task\Development\PackPhar
     */
    protected function taskPackPhar($filename)
    {
        return new Development\PackPhar($filename);
    }

}

 