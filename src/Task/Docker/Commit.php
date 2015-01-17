<?php
namespace Robo\Task\Docker;

use Codegyre\RoboDocker\Result;

class Commit extends Base
{
    protected $command = "docker commit";
    protected $name;
    protected $cid;

    public function __construct($cidOrResult)
    {
        $this->cid = $cidOrResult instanceof Result ? $cidOrResult->getCid() : $cidOrResult;
    }

    public function getCommand()
    {
        return $this->command . ' ' . $this->cid . ' ' . $this->name . ' ' . $this->arguments;
    }

    public function name($name)
    {
        $this->name = $name;
        return $this;
    }

}