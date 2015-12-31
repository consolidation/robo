<?php
namespace Robo\TaskCollection;

use Robo\Contract\TaskInterface;

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
    private static $collection;

    /**
     * Provides direct access to the collection of transients, if necessary.
     */
    public static function getCollection() {
        if (!static::$collection) {
            static::$collection = new Collection();
            register_shutdown_function( function() {
                static::complete();
            });
        }
        return static::$collection;
    }

    /**
     * Register a task that creates transient objects. Its complete
     * function will be called when the program exits.
     */
    public static function transientTask(TaskInterface $task) {
        return new CollectionTask(static::getCollection(), $task);
    }

    /**
     * Call the rollback method of all of the registered objects.
     */
    public static function fail() {
        // Force the rollback and completion functions to run.
        $collection = static::getCollection();
        $collection->fail();
        // Make sure that our completion functions do not run twice.
        $collection->reset();
    }

    /**
     * Call the complete method of all of the registered objects.
     */
    public static function complete() {
        // Run the collection of tasks. This will also run the
        // completion tasks.
        $collection = static::getCollection();
        $collection->run();
        // Make sure that our completion functions do not run twice.
        $collection->reset();
    }
}
