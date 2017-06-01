<?php
namespace Robo\Task\Composer;

trait loadTasks
{
    /**
     * @param null|string $pathToComposer
     *
     * @return Install
     */
    protected function taskComposerInstall($pathToComposer = null)
    {
        return $this->task(Install::class, $pathToComposer);
    }

    /**
     * @param null|string $pathToComposer
     *
     * @return Update
     */
    protected function taskComposerUpdate($pathToComposer = null)
    {
        return $this->task(Update::class, $pathToComposer);
    }

    /**
     * @param null|string $pathToComposer
     *
     * @return DumpAutoload
     */
    protected function taskComposerDumpAutoload($pathToComposer = null)
    {
        return $this->task(DumpAutoload::class, $pathToComposer);
    }

    /**
     * @param null|string $pathToComposer
     *
     * @return Init
     */
    protected function taskComposerInit($pathToComposer = null)
    {
        return $this->task(Init::class, $pathToComposer);
    }

    /**
     * @param null|string $pathToComposer
     *
     * @return Init
     */
    protected function taskComposerConfig($pathToComposer = null)
    {
        return $this->task(Config::class, $pathToComposer);
    }

    /**
     * @param null|string $pathToComposer
     *
     * @return Validate
     */
    protected function taskComposerValidate($pathToComposer = null)
    {
        return $this->task(Validate::class, $pathToComposer);
    }

    /**
     * @param null|string $pathToComposer
     *
     * @return Remove
     */
    protected function taskComposerRemove($pathToComposer = null)
    {
        return $this->task(Remove::class, $pathToComposer);
    }

    /**
     * @param null|string $pathToComposer
     *
     * @return Remove
     */
    protected function taskComposerRequire($pathToComposer = null)
    {
        return $this->task(RequireDependency::class, $pathToComposer);
    }

    /**
     * @param null|string $pathToComposer
     *
     * @return Remove
     */
    protected function taskComposerCreateProject($pathToComposer = null)
    {
        return $this->task(CreateProject::class, $pathToComposer);
    }
}
