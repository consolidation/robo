<?php
namespace Robo\Task;

class Server extends Exec {

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