<?php

namespace Robo\Collection;

use Robo\Contract\TaskInterface;

/**
 * The temporary collection keeps track of the global collection of
 * temporary cleanup tasks in instances where temporary-generating
 * tasks are executed directly via their run() method, rather than
 * as part of a collection.
 *
 * In general, temporary-generating tasks should always be run in
 * a collection, as the cleanup functions registered with the
 * Temporary collection will not run until requested.
 *
 * Since the results could be undefined if cleanup functions were called
 * at arbitrary times during a program's execution, cleanup should only
 * be done immeidately prior to program termination, when there is no
 * danger of cleaning up after some unrelated task.
 *
 * An application need never use Temporary directly, save to
 * call Temporary::wrap() inside loadTasks or loadShortcuts, and
 * to call Temporary::complete() immediately prior to terminating.
 * This is recommended, but not required; this function will be
 * registered as a shutdown function, and called on termination.
 */
class Temporary
{
    private static $collection;

    /**
     * Provides direct access to the collection of temporaries, if necessary.
     */
    public static function getCollection()
    {
        if (!static::$collection) {
            static::$collection = new Collection();
            register_shutdown_function(function () {
                static::complete();
            });
        }

        return static::$collection;
    }

    /**
     * Wrap the given task in a wrapper class that will ensure
     * that its 'complete()' function is called when the program
     * terminates, if not sooner (e.g. if the task is added to
     * some other collection).
     */
    public static function wrap(TaskInterface $task)
    {
        return new TaskWrapper(static::getCollection(), $task);
    }

    /**
     * Call the rollback method of all of the registered objects.
     */
    public static function fail()
    {
        // Force the rollback and completion functions to run.
        $collection = static::getCollection();
        $collection->fail();
        // Make sure that our completion functions do not run twice.
        $collection->reset();
    }

    /**
     * Call the complete method of all of the registered objects.
     */
    public static function complete()
    {
        // Run the collection of tasks. This will also run the
        // completion tasks.
        $collection = static::getCollection();
        $collection->run();
        // Make sure that our completion functions do not run twice.
        $collection->reset();
    }
}
