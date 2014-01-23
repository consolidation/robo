<?php
namespace Robo\Add;

trait Exec  {

    protected function taskExec($command)
    {
        return new \Robo\Task\Exec($command);

    }

}
 