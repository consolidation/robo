<?php
namespace Robo\Task\Composer;

trait loadTasks
{
    /**
     * @param null $pathToComposer
     * @return Install
     */
    protected function taskComposerInstall($pathToComposer = null)
    {
        return $this->task('ComposerInstall', $pathToComposer);
    }

    /**
     * @param null $pathToComposer
     * @return Update
     */
    protected function taskComposerUpdate($pathToComposer = null)
    {
        return $this->task('ComposerUpdate', $pathToComposer);
    }

    /**
     * @param null $pathToComposer
     * @return DumpAutoload
     */
    protected function taskComposerDumpAutoload($pathToComposer = null)
    {
        return $this->task('ComposerDumpAutoload', $pathToComposer);
    }
}
