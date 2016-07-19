<?php

namespace Robo\Collection;

use Robo\Task\BaseTask;
use Robo\Contract\TaskInterface;
use Robo\Contract\RollbackInterface;
use Robo\Contract\CompletionInterface;
use Robo\Contract\WrappedTaskInterface;

/**
 * Creates a task wrapper that will manage rollback and collection
 * management to a task when it runs.  Tasks are automatically
 * wrapped in a CompletionWrapper when added to a task collection.
 *
 * Clients may need to wrap their task in a CompletionWrapper if it
 * creates temporary objects.
 *
 * @see Robo\Task\Filesystem\loadTasks::taskTmpDir
 */
class CompletionWrapper extends BaseTask implements WrappedTaskInterface
{
    private $collection;
    private $task;
    private $rollbackTask;

    /**
     * Create a CompletionWrapper.
     *
     * Temporary tasks are always wrapped in a CompletionWrapper, as are
     * any tasks that are added to a collection.  If a temporary task
     * is added to a collection, then it is first unwrapped from its
     * CompletionWrapper (via its original() method), and then added to a
     * new CompletionWrapper for the collection it is added to.
     *
     * In this way, when the CompletionWrapper is finally executed, the
     * task's rollback and completion handlers will be registered on
     * whichever collection it was registered on.
     */
    public function __construct(Collection $collection, TaskInterface $task, TaskInterface $rollbackTask = null)
    {
        $this->collection = $collection;
        $this->task = ($task instanceof WrappedTaskInterface) ? $task->original() : $task;
        $this->rollbackTask = $rollbackTask;
    }

    public function original()
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
            $this->collection->registerRollback(new CallableTask([$this->task, 'rollback'], $this->task));
        }
        if ($this->task instanceof CompletionInterface) {
            $this->collection->registerCompletion(new CallableTask([$this->task, 'complete'], $this->task));
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
