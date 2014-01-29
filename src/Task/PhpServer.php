<?php
namespace Robo\Task;

trait PhpServer
{
    protected function taskServer($port)
    {
        $server = new PhpServerTask($port);
        $this->runningCommands[] = $server;
        return $server;
    }
}

class PhpServerTask extends ExecTask
{
    public function __construct($port = 8000)
    {
        $this->command = "php -S 127.0.0.1:$port ";
    }

    public function dir($path)
    {
        $this->command .= "-t $path";
        return $this;
    }

} 