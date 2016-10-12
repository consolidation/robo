<?php
namespace Robo\Task\Base;

trait loadShortcuts
{
    /**
     * Executes shell command
     *
     * @param $command
     * @return \Robo\Result
     */
    protected function _exec($command)
    {
        return $this->taskExec($command)->run();
    }
}
