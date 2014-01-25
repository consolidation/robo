<?php
namespace Robo\Add;

trait PackPhar {

    /**
     * @param $filename
     * @return \Robo\Task\PackPhar
     */
    protected function taskPackPhar($filename)
    {
        return new \Robo\Task\PackPhar($filename);
    }

} 