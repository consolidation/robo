<?php
namespace Robo\Add;

use Robo\Task\Server;

trait Exec  {

    private $runningCommands = [];

    protected function taskExec($command)
    {
        $exec = new \Robo\Task\Exec($command);
        $this->runningCommands[] = $exec;
        return $exec;
    }

    protected function taskServer($port)
    {
        $server = new Server($port);
        $this->runningCommands[] = $server;
        return $server;
    }

}
 