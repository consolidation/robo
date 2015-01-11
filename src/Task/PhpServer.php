<?php
namespace Robo\Task;
trait_exists('Robo\Task\Exec', true);

/**
 * Start PHP Server and
 */
trait PhpServer
{
    protected function taskServer($port)
    {
        $server = new Development\PhpServer($port);
        $this->runningCommands[] = $server;
        return $server;
    }
}