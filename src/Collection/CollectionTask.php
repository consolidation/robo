<?php

namespace Robo\Collection;

use Robo\Task\BaseTask;
use Robo\Contract\TaskInterface;

/**
 * Creates a task wrapper that will manage rollback and collection
 * management to a task when it runs.  Tasks are automatically
 * wrapped in a CollectionTask when added to a task collection.
 *
 * Clients may need to wrap their task in a CollectionTask if it
 * creates temporary objects.  This is usually best done via
 * Temporary::temporaryTask().
 *
 * @see Robo\Task\FileSystem\loadTasks::taskTmpDir
 */
class CollectionTask extends BaseTask
{
    private $collection;
    private $task;
    private $rollbackTask;

    /**
     * Create a CollectionTask.
     *
     * Temporary tasks are always wrapped in a CollectionTask, as are
     * any tasks that are added to a collection.  If a temporary task
     * is added to a collection, then it is first unwrapped from its
     * CollectionTask (via its getTask method), and then added to a
     * new CollectionTask for the collection it is added to.
     *
     * In this way, when the CollectionTask is finally executed, the
     * task's rollback and completion handlers will be registered on
     * whichever collection it was registered on.
     */
    public function __construct(Collection $collection, TaskInterface $task, TaskInterface $rollbackTask = null)
    {
        $this->collection = $collection;
        $this->task = ($task instanceof self) ? $task->getTask() : $task;
        $this->rollbackTask = $rollbackTask;
    }

    public function getTask()
    {
        return $this->task;
    }

    /**
     * The purpose of this function.  Once a collection task runs,
     * register it on its collection.
     */
    public function run()
    {
        if ($this->rollbackTask) {
            $this->collection->registerRollback($this->rollbackTask);
        }
        $this->collection->registerRollbackAndCompletionHandlers($this->task);

        return $this->task->run();
    }

    /**
     * Make this wrapper object act like the class it wraps.
     */
    public function __call($function, $args)
    {
        return call_user_func_array(array($this->task, $function), $args);
    }
}
