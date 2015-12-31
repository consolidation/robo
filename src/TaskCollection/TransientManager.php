<?php
namespace Robo\TaskCollection;

/**
 * The transient manager keeps track of the global collection of
 * transient cleanup tasks in instances where transient-generating
 * tasks are executed directly via their run() method, rather than
 * as part of a collection.
 *
 * In general, transient-generating tasks should always be run in
 * a collection, as the cleanup functions registered with the
 * TransientManager will not run until requested.
 *
 * Since the results could be undefined if cleanup functions were called
 * at arbitrary times during a program's execution, cleanup should only
 * be done immeidately prior to program termination, when there is no
 * danger of cleaning up after some unrelated task.
 *
 * An application need never use the TransientManager directly, save to
 * call TransientManager::complete() immediately prior to terminating.
 */
class TransientManager
{
    static $collection;

    public static function getCollection() {
        if (!static::$collection) {
            static::setCollection(new Collection());
        }
        return static::$collection;
    }

    public static function setCollection(Collection $collection) {
        return static::$collection = $collection;
    }

    public static function fail() {
        // Force the rollback and completion functions to run.
        $collection = static::getCollection();
        $collection->fail();
        // Make sure that our completion functions do not run twice.
        static::setCollection(NULL);
    }

    public static function complete() {
        // Run the collection of tasks. This will also run the
        // completion tasks.
        $collection = static::getCollection();
        $collection->run();
        // Make sure that our completion functions do not run twice.
        static::setCollection(NULL);
    }
}
