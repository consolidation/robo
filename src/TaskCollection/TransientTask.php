<?php
namespace Robo\TaskCollection;

use Robo\Contract\TaskInterface;

/**
 * Use TransientTask in any CollectionAwareTask that generates transients.
 * Simply call $collection->registerTransient() from within your
 * runInCollection($task) method, and the cleanup task that you pass in will
 * run on completion (or only on rollback, if keepIfSuccessful() is called).
 *
 * @see Robo\Task\FileSystem\TmpDir
 */
trait TransientTask
{
    protected $registerFunction = 'registerCompletion';

    /**
     * Prevent cleanup tasks from being executed when the task collection
     * completes successfully.
     */
    public function keepIfSuccessful() {
        $this->registerFunction = 'registerRollback';
        return $this;
    }

    /**
     * Add a cleanup task to the completion stack of the provided collection.
     */
    public function registerTransient(Collection $collection, TaskInterface $task) {
        $collection->{$this->registerFunction}($task);
    }
}
