<?php
namespace Robo\Task\Base;

trait loadShortcuts
{
    /**
     * Executes shell command
     *
     * @param string|\Robo\Contract\CommandInterface $command
     *
     * @return \Robo\Result
     */
    protected function _exec($command)
    {
        return $this->taskExec($command)->run();
    }
}
