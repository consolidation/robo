<?php
namespace Robo\Task\Npm;

trait loadTasks
{
    /**
     * @param null $pathToNpm
     * @return Install
     */
    protected function taskNpmInstall($pathToNpm = null) {
        return $this->task(__FUNCTION__, $pathToNpm);
    }

    /**
     * @param null $pathToNpm
     * @return Update
     */
    protected function taskNpmUpdate($pathToNpm = null) {
        return $this->task(__FUNCTION__, $pathToNpm);
    }
}
