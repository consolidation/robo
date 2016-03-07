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
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Composer\Install',
            [$pathToComposer]
        );
    }

    /**
     * @param null $pathToComposer
     * @return Update
     */
    protected function taskComposerUpdate($pathToComposer = null)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Composer\Update',
            [$pathToComposer]
        );
    }

    /**
     * @param null $pathToComposer
     * @return DumpAutoload
     */
    protected function taskComposerDumpAutoload($pathToComposer = null)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Composer\DumpAutoload',
            [$pathToComposer]
        );
    }

}
