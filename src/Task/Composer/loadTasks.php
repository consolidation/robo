<?php
namespace Robo\Task\Composer;

use Robo\Container\SimpleServiceProvider;

trait loadTasks
{
    /**
     * Return services.
     */
    public static function getComposerServices()
    {
        return new SimpleServiceProvider(
            [
                'taskComposerInstall' => Install::class,
                'taskComposerUpdate' => Update::class,
                'taskComposerDumpAutoload' => DumpAutoload::class,
                'taskComposerValidate' => Validate::class,
            ]
        );
    }

    /**
     * @param null $pathToComposer
     * @return Install
     */
    protected function taskComposerInstall($pathToComposer = null)
    {
        return $this->task(__FUNCTION__, $pathToComposer);
    }

    /**
     * @param null $pathToComposer
     * @return Update
     */
    protected function taskComposerUpdate($pathToComposer = null)
    {
        return $this->task(__FUNCTION__, $pathToComposer);
    }

    /**
     * @param null $pathToComposer
     * @return DumpAutoload
     */
    protected function taskComposerDumpAutoload($pathToComposer = null)
    {
        return $this->task(__FUNCTION__, $pathToComposer);
    }

    /**
     * @param null $pathToComposer
     * @return Validate
     */
    protected function taskComposerValidate($pathToComposer = null)
    {
        return $this->task(__FUNCTION__, $pathToComposer);
    }
}
