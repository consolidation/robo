<?php
namespace Robo\Task\Npm;

trait loadTasks 
{
    /**
     * @param null $pathToNpm
     * @return Install
     */
    protected function taskNpmInstall($pathToNpm = null) {
        return new Install($pathToNpm);
    }

    /**
     * @param null $pathToNpm
     * @return Update
     */
    protected function taskNpmUpdate($pathToNpm = null) {
        return new Update($pathToNpm);
    }
} 