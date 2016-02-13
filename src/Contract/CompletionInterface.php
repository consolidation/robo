<?php
namespace Robo\Contract;

/**
 * Any Robo tasks that implements this interface will
 * be called when the task collection it is added to
 * completes.
 *
 * Tasks that should be cleaned up when the program
 * terminates whenever they are used outside of a
 * task collection should be wrapped in
 * Temporary::wrap().  This will cause their
 * complete() method to be called at shutdown time, but
 * only if the object is not added to some other collection.
 *
 * @see Robo\Task\FileSystem\loadTasks::taskTmpDir
 *
 * Interface CompletionInterface
 * @package Robo\Contract
 */
interface CompletionInterface
{
    /**
     * Revert an operation that can be rolled back
     */
    public function complete();
}
