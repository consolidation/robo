<?php
namespace Robo\Task\Assets;

trait loadTasks 
{
    /**
     * @param $input
     * @return Minify
     */
    protected function taskMinify($input)
    {
        return new Minify($input);
    }
} 