<?php
namespace Robo\Task\Bower;

use Robo\Container\SimpleServiceProvider;

trait loadTasks
{
    /**
     * Return services.
     */
    public static function getBowerServices()
    {
        return new SimpleServiceProvider(
            [
                'taskBowerInstall' => Install::class,
                'taskBowerUpdate' => Update::class,
            ]
        );
    }

    /**
     * @param null $pathToBower
     * @return Install
     */
    protected function taskBowerInstall($pathToBower = null)
    {
        return $this->task(Install::class, $pathToBower);
    }

    /**
     * @param null $pathToBower
     * @return Update
     */
    protected function taskBowerUpdate($pathToBower = null)
    {
        return $this->task(Update::class, $pathToBower);
    }
}
