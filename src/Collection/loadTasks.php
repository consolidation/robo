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
                'collection' => Collection::class,
                'taskForEach' => TaskForEach::class,
            ]
        );
    }

    /**
     * Convenience function. Use:
     *
     * $this->collection();
     *
     * instead of:
     *
     * $this->getContainer()->get('collection');
     *
     * @return Collection
     */
    protected function collection()
    {
        return $this->getContainer()->get('collection');
    }

    /**
     * Run a callback function on each item in a collection
     *
     * @param array|Iterable $collection
     * @return ForEach
     */
    protected function taskForEach($collection)
    {
        return $this->task(__FUNCTION__, $collection);
    }
}
