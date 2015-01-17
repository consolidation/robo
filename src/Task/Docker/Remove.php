<?php
namespace Robo\Task\Docker;

class Remove extends Base
{
    function __construct($container)
    {
        $this->command = "docker rm $container ";
    }
}