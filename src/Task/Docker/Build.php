<?php
namespace Robo\Task\Docker;

class Build extends Base
{
    protected $path;

    public function __construct($path = '.')
    {
        $this->command = "docker build";
        $this->path = $path;
    }

    public function getCommand()
    {
        return $this->command . ' ' . $this->arguments . ' ' . $this->path;
    }

    public function tag($tag)
    {
        return $this->option('-t', $tag);
    }

    public function run()
    {
        $this->printTaskInfo("Running <info>{$this->command}</info>");
        $this->executeCommand($this->getCommand());
    }
}