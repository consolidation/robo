<?php
namespace Robo\Task\Base;

trait loadShortcuts 
{
    protected function _exec($command)
    {
        return (new Exec($command))->run();
    }
} 