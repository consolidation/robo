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
     * @return Archive
     */
    protected function taskPack($filename)
    {
        return $this->task(__FUNCTION__, $filename);
    }

    /**
     * @param $filename
     *
     * @return Extract
     */
    protected function taskExtract($filename)
    {
        return $this->task(__FUNCTION__, $filename);
    }
}
