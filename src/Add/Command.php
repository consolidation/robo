<?php
namespace Robo\Add;

trait Command {

    protected function taskCommand($command)
    {
        return new \Robo\Task\Command($command);
    }

}
 