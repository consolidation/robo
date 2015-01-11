<?php

namespace Robo\Task;

use Robo\Output;
use Robo\Result;
use Robo\Task\Shared\TaskInterface;

trait Minify
{
    /**
     * @param bool|string $input
     *
     * @return \Robo\Task\Assets\Minify
     */
    protected function taskMinify( $input )
    {
        return new Assets\Minify( $input );
    }
}
