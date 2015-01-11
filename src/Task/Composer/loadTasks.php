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
        return new Install($pathToComposer);
    }

    /**
     * @param null $pathToComposer
     * @return Update
     */
    protected function taskComposerUpdate($pathToComposer = null)
    {
        return new Update($pathToComposer);
    }

    /**
     * @param null $pathToComposer
     * @return DumpAutoload
     */
    protected function taskComposerDumpAutoload($pathToComposer = null)
    {
        return new DumpAutoload($pathToComposer);
    }

} 