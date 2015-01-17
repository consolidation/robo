<?php
namespace Robo\Task\Docker;

class Pull extends Base
{
    function __construct($image)
    {
        $this->command = "docker pull $image ";
    }
}