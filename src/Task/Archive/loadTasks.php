<?php
namespace Robo\Task\Archive;

use Robo\Container\SimpleServiceProvider;

trait loadTasks
{
    /**
     * Return services.
     */
    public static function getArchiveServices()
    {
        return new SimpleServiceProvider(
            [
                'taskExtract' => Extract::class,
                'taskPack' => Pack::class,
            ]
        );
    }

    /**
     * @param $filename
     *
     * @return Pack
     */
    protected function taskPack($filename)
    {
        return $this->task(Pack::class, $filename);
    }

    /**
     * @param $filename
     *
     * @return Extract
     */
    protected function taskExtract($filename)
    {
        return $this->task(Extract::class, $filename);
    }
}
