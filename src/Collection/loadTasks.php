<?php
namespace Robo\Collection;

use Robo\Container\SimpleServiceProvider;

trait loadTasks
{
    /**
     * Return services.
     */
    public static function getCollectionServices()
    {
        return new SimpleServiceProvider(
            [
                'taskForEach' => TaskForEach::class,
            ]
        );
    }

    /**
     * Run a callback function on each item in a collection
     *
     * @param array|Iterable $collection
     * @return ForEach
     */
    protected function taskForEach($collection)
    {
        return $this->task(TaskForEach::class, $collection);
    }
}
