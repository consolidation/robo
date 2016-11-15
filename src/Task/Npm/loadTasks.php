<?php
namespace Robo\Task\Npm;

trait loadTasks
{
    /**
     * @param null|string $pathToNpm
     *
     * @return \Robo\Task\Npm\Install
     */
    protected function taskNpmInstall($pathToNpm = null)
    {
        return $this->task(Install::class, $pathToNpm);
    }

    /**
     * @param null|string $pathToNpm
     *
     * @return \Robo\Task\Npm\Update
     */
    protected function taskNpmUpdate($pathToNpm = null)
    {
        return $this->task(Update::class, $pathToNpm);
    }
}
