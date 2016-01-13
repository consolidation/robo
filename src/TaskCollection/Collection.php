<?php
namespace Robo\TaskCollection;

use Robo\Result;
use Robo\Contract\TaskInterface;
use Robo\Contract\RollbackInterface;
use Robo\Contract\CompletionInterface;

/**
 * Group tasks into a collection that run together. Supports
 * rollback operations for handling error conditions.
 *
 * Below, the example FilesystemStack task is added to a collection,
 * and associated with a rollback task.  If any of the operations in
 * the FileSystemStack, or if any of the other tasks also added to
 * the task collection should fail, then the rollback function is
 * called. Often, taskDeleteDir is used to remove partial results
 * of an unfinished task.
 *
 * ``` php
 * <?php
 * $collection = $this->taskCollection();
 * $this->taskFileSystemStack()
 *      ->mkdir('logs')
 *      ->touch('logs/.gitignore')
 *      ->chgrp('logs', 'www-data')
 *      ->symlink('/var/log/nginx/error.log', 'logs/error.log')
 *      ->runLater($collection, $this->taskDeleteDir('logs'));
 * /// ... add other tasks to collection via runLater()
 * $collection->runNow();
 *
 * ?>
 * ```
 */
class Collection implements TaskInterface
{
    // Unnamed tasks are assigned an arbitrary numeric index
    // in the task list. Any numeric value may be used.
    const UNNAMEDTASK = 0;

    protected $taskStack = [];
    protected $rollbackStack = [];
    protected $completionStack = [];
    protected $frozen = false;

    /**
     * Add a task or a list of tasks to our task collection.  Each task
     * will run via its 'run()' method once (and if) all of the tasks
     * added before it complete successfully.  If the task also implements
     * RollbackInterface, then it will be rolled back via its 'rollback()'
     * method ONLY if its 'run()' method completes successfully, and some
     * task added after it fails.
     *
     * @param string|TaskInterface|TaskInterface[]
     *   An optional name for the task -- missing or NULL for unnamed tasks.
     *   Names are used for positioning before and after tasks.  If
     *   a name is not provided, then the first parameter may contain
     *   a task to add, or an array of tasks to add.
     * @param TaskInterface
     *   If the first parameter is a string, then the second parameter
     *   holds a single task to add to our collection.
     */
    public function add($name, $task = NULL)
    {
        if (is_array($name)) {
            return $this->addTaskList($name);
        }
        if (isset($name) && is_string($name)) {
            return $this->addTask($name, $task);
        }
        return $this->addTask(self::UNNAMEDTASK, $task);
    }

    /**
     * Add a rollback task to our task collection.  A rollback task
     * will execute ONLY if all of the tasks added before it complete
     * successfully, AND some task added after it fails.
     *
     * @param TaskInterface
     *   The rollback task to add.  Note that the 'run()' method of the
     *   task executes, not its 'rollback()' method.  To use the 'rollback()'
     *   method, add the task via 'Collection::add()' instead.
     */
    public function rollback($rollbackTask) {
        $this->addToTaskStack($name, new CollectionTask(0, NULL, $rollbackTask));
        return $this;
    }

    /**
     * Add a list of tasks to our task collection.
     *
     * @param TaskInterface[]
     *   An array of tasks to run with rollback protection
     */
    protected function addTaskList($tasks)
    {
        foreach ($tasks as $name => $task) {
            $this->addTask($name, $task);
        }
        return $this;
    }

    /**
     * Add a task to our task collection.  If there is a later failure,
     * then run the provided rollback operation.  The rollback() method of
     * the task will also be executed, if the task implements RollbackInterface.
     *
     * @param string
     *   A name for the task, used for positioning before and after tasks.
     * @param TaskInterface
     *   The task to run
     * @param TaskInterface
     *   The rollback function to run if any command in the collection fails.
     *   n.b. Rollback is done via $rollbackTask->run() and $task->rollback().
     */
    public function addTask($name, TaskInterface $task, TaskInterface $rollbackTask = null)
    {
        $this->addToTaskStack($name, new CollectionTask($this, $task, $rollbackTask));
        return $this;
    }

    /**
     * Add a task to our task stack; when it runs, ignore any errors that
     * it may generate.
     *
     * @param string
     *   A name for the task, used for positioning before and after tasks.
     * @param TaskInterface
     *   The task to run
     */
    public function addAndIgnoreErrors($name, TaskInterface $task)
    {
        $this->addToTaskStack($name, new IgnoreErrorsTaskWrapper($task));
        return $this;
    }

    /**
     * Add the provided task to our task list.
     */
    protected function addToTaskStack($name, TaskInterface $task)
    {
        $this->checkFrozen();
        // If a task name is not provided, then we'll let php pick
        // the array index.
        if (static::isUnnamedTask($name)) {
            $this->taskStack[] = $task;
        } else {
            // If we are replacing an existing task with the
            // same name, ensure that our new task is added to
            // the end.
            // TODO: This could have bad consequences.  Perhaps
            // we should just `throw` here.  We should probably
            // encourage folks to use a naming convention such
            // as 'groupname.taskname', in case clients group
            // collections from multiple sources.
            unset($this->taskStack[$name]);
            $this->taskStack[$name] = $task;
        }
    }

    /**
     * Test to see if the given name is an unnamed task, or
     * something functionally equivalent.  Any numeric index,
     * for example, is renumbered when added to the collection.
     */
    public static function isUnnamedTask($name)
    {
        // Note that the comparison with UNNAMEDTASK is, strictly speaking,
        // unnecessary.  It is included for clarity.
        return ($name == self::UNNAMEDTASK) ||
            !is_string($name) ||
            empty($name) ||
            is_numeric($name);
    }

    /**
     * Register a rollback task to run if there is any failure.
     *
     * Clients are free to add tasks to the rollback stack as
     * desired; however, usually it is preferable to call
     * Collection::addWithRollback() instead.  With that function,
     * the rollback function will only be called if its associated
     * task completes successfully, AND some later task fails.
     *
     * One example of a good use-case for registering a callback
     * function directly is to add a task that sends notification
     * when a task fails.
     *
     * @param TaskInterface
     *   The rollback task to run on failure.
     */
    public function registerRollback(TaskInterface $rollbackTask)
    {
        $this->rollbackStack[] = $rollbackTask;
        return $this;
    }

    /**
     * Register a completion task to run once all other tasks finish.
     * Completion tasks run whether or not a rollback operation was
     * triggered. They do not trigger rollbacks if they fail.
     *
     * The typical use-case for a completion function is to clean up
     * transient objects (e.g. temporary folders).
     *
     * On failures, completion tasks will run after all rollback tasks.
     * If one task collection is nested inside another task collection,
     * then the nested collection's completion tasks will run as soon as
     * the nested task completes; they are not deferred to the end of
     * the containing collection's execution.
     *
     * @param TaskInterface
     *   The completion task to run at the end of all other operations.
     */
    public function registerCompletion(TaskInterface $completionTask)
    {
        $this->completionStack[] = $completionTask;
        return $this;
    }

    /**
     * Try to run our tasks.  If any of them fail, then run all
     * of our rollback tasks.
     *
     * Note that this is a synonym for run(); it is included for
     * symetry with the runLater() method of Collectable.
     */
    public function runNow()
    {
        return $this->run();
    }

    /**
     * Run our tasks, and roll back if necessary.
     */
    public function run()
    {
        $this->freezeCollection();
        $result = $this->runTaskList($this->taskStack);
        if (!$result->wasSuccessful()) {
            $this->runRollbackTasks();
        }
        $this->complete();
        return $result;
    }

    /**
     * Register a task for rollback and completion handling, but
     * do NOT add it to the execution queue.
     *
     * This usually happens automatically, via CollectionTask
     * TODO: protected?
     */
    public function register($task)
    {
        if ($task instanceof RollbackInterface) {
            $this->registerRollback(new RollbackTask($task));
        }
        if ($task instanceof CompletionInterface) {
            $this->registerCompletion(new CompletionTask($task));
        }
        return $this;
    }

    /**
     * Force the rollback functions to run
     */
    public function fail()
    {
        $this->runRollbackTasks();
        $this->complete();
        return $this;
    }

    /**
     * Force the completion functions to run
     */
    public function complete()
    {
        $this->runTaskListIgnoringFailures($this->completionStack);
        return $this;
    }

    /**
     * Reset this collection, removing all tasks.
     */
    public function reset()
    {
        $this->taskStack = [];
        $this->completionStack = [];
        $this->rollbackStack = [];
        $this->frozen = false;
        return $this;
    }

    /**
     * Run all of our rollback tasks.
     *
     * Note that Collection does not implement RollbackInterface, but
     * it may still be used as a task inside another task collection
     * (i.e. you can nest task collections, if desired).
     */
    protected function runRollbackTasks()
    {
        $this->runTaskListIgnoringFailures($this->rollbackStack);
        // Erase our rollback stack once we have finished rolling
        // everything back.  This will allow us to potentially use
        // a command collection more than once (e.g. to retry a
        // failed operation after doing some error recovery).
        $this->rollbackStack = [];
    }

    /**
     * Run every task in a list, but only up to the first failure.
     * Return the failing result, or success if all tasks run.
     */
    protected function runTaskList($taskList)
    {
        try {
            foreach ($taskList as $task) {
                $result = $task->run();
                // If the current task returns an error code, then stop
                // execution and signal a rollback.
                if (($result instanceof Result) && (!$result->wasSuccessful())) {
                    return $result;
                }
            }
        } catch (Exception $e) {
            // Tasks typically do not throw, but if one does, we will
            // convert it into an error and roll back.
            // TODO: should we re-throw it again instead?
            $result = new Result($this, -1, $e->getMessage());
        }
        return Result::success($this);
    }

    /**
     * Run all of the tasks in a provided list, ignoring failures.
     */
    protected function runTaskListIgnoringFailures($taskList)
    {
        foreach ($taskList as $task) {
            try {
                $task->run();
            } catch (Exception $e) {
                // Ignore rollback failures.
            }
        }
        return Result::success($this);
    }

    /**
     * Once the collection has been executed at least once,
     * prevent additional tasks from being added to it.  It
     * is okay to run a collection multiple times, e.g. to
     * retry a failed operation; however, calling `add()` after
     * `run()` is generally not useful, and is probably an
     * indication of a logic error.
     */
    protected function freezeCollection()
    {
        $this->frozen = true;
    }

    /**
     * Do not allow frozen collections to be modified. This should
     * never happen, so we'll just throw a RuntimeException.
     */
    protected function checkFrozen()
    {
        if ($this->frozen) {
            throw new RuntimeException("Collection cannot be modified after execution.");
        }
    }
}
