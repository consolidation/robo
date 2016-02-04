<?php

namespace Robo\Collection;

use Robo\Task\BaseTask;
use Robo\Contract\TaskInterface;
use Robo\Contract\RollbackInterface;
use Robo\Contract\CompletionInterface;

/**
 * Creates a task wrapper that will manage rollback and collection
 * management to a task when it runs.  Tasks are automatically
 * wrapped in a TaskWrapper when added to a task collection.
 *
 * Clients may need to wrap their task in a TaskWrapper if it
 * creates temporary objects.  This is usually best done via
 * Temporary::wrap().
 *
 * @see Robo\Task\FileSystem\loadTasks::taskTmpDir
 */
class TaskWrapper extends BaseTask
{
    private $collection;
    private $task;
    private $rollbackTask;

    /**
     * Create a TaskWrapper.
     *
     * Temporary tasks are always wrapped in a TaskWrapper, as are
     * any tasks that are added to a collection.  If a temporary task
     * is added to a collection, then it is first unwrapped from its
     * TaskWrapper (via its getTask method), and then added to a
     * new TaskWrapper for the collection it is added to.
     *
     * In this way, when the TaskWrapper is finally executed, the
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
     * Before running this task, register its rollback and completion
     * handlers on its collection. The reason this class exists is to
     * defer registration of rollback and completion tasks until 'run()' time.
     */
    public function run()
    {
        if ($this->rollbackTask) {
            $this->collection->registerRollback($this->rollbackTask);
        }
        if ($this->task instanceof RollbackInterface) {
            $this->collection->registerRollback([$this->task, 'rollback']);
        }
        if ($this->task instanceof CompletionInterface) {
            $this->collection->registerCompletion([$this->task, 'complete']);
        }

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
