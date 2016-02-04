<?php
namespace Robo\Collection;

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
 * $collection = $this->collection();
 * $this->taskFileSystemStack()
 *      ->mkdir('logs')
 *      ->touch('logs/.gitignore')
 *      ->chgrp('logs', 'www-data')
 *      ->symlink('/var/log/nginx/error.log', 'logs/error.log')
 *      ->addToCollection($collection, $this->taskDeleteDir('logs'));
 * /// ... add other tasks to collection via addToCollection()
 * $collection->run();
 *
 * ?>
 * ```
 */
class Collection implements TaskInterface
{
    // Unnamed tasks are assigned an arbitrary numeric index
    // in the task list. Any numeric value may be used, but the
    // UNNAMEDTASK constant is recommended for clarity.
    const UNNAMEDTASK = 0;

    protected $taskStack = [];
    protected $rollbackStack = [];
    protected $completionStack = [];
    protected $incrementalResults;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->incrementalResults = Result::success($this);
    }

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
    public function add($name, $task = null)
    {
        if (is_array($name)) {
            return $this->addTaskList($name);
        }
        return $this->addTask($name, $task);
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
    public function rollback($rollbackTask)
    {
        // Wrap the task as necessary.
        $rollbackTask = $this->collectAndWrapTask($rollbackTask);
        // TODO: Make a rollback registration class to replace this
        // use of CollectionTask, get rid of $rollbackClass in CollectionTask,
        // and delete EmptyTask.
        $this->addToTaskStack($name, new CollectionTask(0, new EmptyTask(), $rollbackTask));
        return $this;
    }

    /**
     * Add a task before an existing named task.
     *
     * @param string
     *   The name of the task to insert before.  The named task MUST exist.
     * @param TaskInterface
     *   The task to add.
     * @param string
     *   The name of the task to add. If not provided, will be associated
     *   with the named task it was added before.
     */
    public function before($name, $task, $nameOfTaskToAdd = self::UNNAMEDTASK)
    {
        // Wrap the task as necessary.
        $task = $this->collectAndWrapTask($task);
        $existingTask = $this->namedTask($name);
        $existingTask->before($task, $nameOfTaskToAdd);
        return $this;
    }

    /**
     * Add a task after an existing named task.
     *
     * @param string
     *   The name of the task to insert before.  The named task MUST exist.
     * @param TaskInterface
     *   The task to add.
     * @param string
     *   The name of the task to add. If not provided, will be associated
     *   with the named task it was added after.
     */
    public function after($name, $task, $nameOfTaskToAdd = self::UNNAMEDTASK)
    {
        // Wrap the task as necessary.
        $task = $this->collectAndWrapTask($task);
        $existingTask = $this->namedTask($name);
        $existingTask->after($task, $nameOfTaskToAdd);
        return $this;
    }

    public function taskNames()
    {
        return array_keys($this->taskStack);
    }

    /**
     * Find an existing named task.
     *
     * @param string
     *   The name of the task to insert before.  The named task MUST exist.
     * @returns Element
     *   The task group for the named task. Generally this is only
     *   used to call 'before()' and 'after()'.
     */
    protected function namedTask($name)
    {
        if (!$this->hasTask($name)) {
            throw new \RuntimeException("Could not find task named $name");
        }
        return $this->taskStack[$name];
    }

    public function hasTask($name)
    {
        return array_key_exists($name, $this->taskStack);
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
     */
    public function addTask($name, TaskInterface $task)
    {
        // Wrap the task as necessary.
        $task = $this->collectAndWrapTask($task);
        $this->addToTaskStack($name, new CollectionTask($this, $task));
        return $this;
    }

    /**
     * If the task needs to be wrapped, create whatever wrapper objects are
     * needed for it.
     */
    protected function collectAndWrapTask($task)
    {
        // If the task implements `CollectedInterface`, then tell it it was
        // collected. Collection may involve the creation of a wrapper,
        // or it may return the same task.
        if ($task instanceof CollectedInterface) {
            $task = $task->collected($this);
        }
        // If the caller provided a function pointer instead of a TaskInstance,
        // then wrap it in a CallableTask.
        if (is_callable($task)) {
            $task = new CallableTask($task, $this);
        }
        return $task;
    }

    /**
     * Add the provided task to our task list.
     */
    protected function addToTaskStack($name, $task)
    {
        // All tasks are stored in a task group so that we have a place
        // to hang 'before' and 'after' tasks.
        $taskGroup = new Element($task);
        // If a task name is not provided, then we'll let php pick
        // the array index.
        if (static::isUnnamedTask($name)) {
            $this->taskStack[] = $taskGroup;
            return $this;
        }
        // If we are replacing an existing task with the
        // same name, ensure that our new task is added to
        // the end.
        $this->taskStack[$name] = $taskGroup;
        return $this;
    }

    /**
     * Test to see if the given name is an unnamed task, or
     * something functionally equivalent.  Any numeric index
     * is renumbered when added to the collection.
     */
    public static function isUnnamedTask($name)
    {
        return is_numeric($name);
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
    public function registerRollback($rollbackTask)
    {
        // Wrap the task as necessary.
        $rollbackTask = $this->collectAndWrapTask($rollbackTask);
        if ($rollbackTask) {
            $this->rollbackStack[] = $rollbackTask;
        }
        return $this;
    }

    /**
     * Register a completion task to run once all other tasks finish.
     * Completion tasks run whether or not a rollback operation was
     * triggered. They do not trigger rollbacks if they fail.
     *
     * The typical use-case for a completion function is to clean up
     * temporary objects (e.g. temporary folders).
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
    public function registerCompletion($completionTask)
    {
        // Wrap the task as necessary.
        $completionTask = $this->collectAndWrapTask($completionTask);
        if ($completionTask) {
            $this->completionStack[] = $completionTask;
        }
        return $this;
    }

    /**
     * Run our tasks, and roll back if necessary.
     */
    public function run()
    {
        $result = $this->runPreservingTemporaries();
        $this->complete();
        return $result;
    }

    /**
     * Like 'run()', but does not delete temporaries.
     * Allows caller to continue adding tasks to the
     * same collection, e.g. perhaps to re-use a temporary
     * directory or other temporary which will persist
     * until 'run()' or 'complete()' is called.
     */
    public function runPreservingTemporaries()
    {
        // If there were some tasks that were run before, and they
        // failed, subsequent calls to run() will do nothing further,
        // and will continue to return the same error result.
        $result = $this->getIncrementalResults();
        if ($result->wasSuccessful()) {
            foreach ($this->taskStack as $name => $taskGroup) {
                $taskList = $taskGroup->getTaskList();
                $result = $this->runTaskList($name, $taskList, $result);
                if (!$result->wasSuccessful()) {
                    $this->fail();
                    return $result;
                }
            }
            $taskStack = [];
        }
        return $result;
    }

    /**
     * Register a task for rollback and completion handling, but
     * do NOT add it to the execution queue.
     *
     * This should be called after the task executes.  See
     * CollectionTask.
     */
    public function registerRollbackAndCompletionHandlers($task)
    {
        if ($task instanceof RollbackInterface) {
            $this->registerRollback([$task, 'complete']);
        }
        if ($task instanceof CompletionInterface) {
            $this->registerCompletion([$task, 'complete']);
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
        $this->reset();
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
        $this->incrementalResults = Result::success($this);
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
    protected function runTaskList($name, $taskList, $incrementalResults)
    {
        $result = $incrementalResults;
        try {
            foreach ($taskList as $taskName => $task) {
                $result = $task->run();
                // If the current task returns an error code, then stop
                // execution and signal a rollback.
                if (!$result->wasSuccessful()) {
                    return $result;
                }
                // We accumulate our results into a field so that tasks that
                // have a reference to the collection may examine and modify
                // the incremental results, if they wish.
                $result = $this->accumulateResults(static::isUnnamedTask($taskName) ? $name : $taskName, $result);
            }
        } catch (Exception $e) {
            // Tasks typically should not throw, but if one does, we will
            // convert it into an error and roll back.
            // TODO: should we re-throw it again instead?
            return new Result($this, -1, $e->getMessage());
        }
        return $result;
    }

    public function accumulateResults($taskName, Result $result)
    {
        // If the result is not set or is not a Result, then
        // do nothing.
        if (isset($result) && ($result instanceof Result)) {
            // If the task is unnamed, then all of its data elements
            // just get merged in at the top-level of the final Result object.
            if (static::isUnnamedTask($taskName)) {
                $this->incrementalResults->merge($result);
            } elseif (isset($this->incrementalResults[$taskName])) {
                // There can only be one task with a given name; however, if
                // there are tasks added 'before' or 'after' the named task,
                // then the results from these will be stored under the same
                // name unless they are given a name of their own when added.
                $this->incrementalResults[$taskName]->merge($result);
            } else {
                $this->incrementalResults[$taskName] = $result;
            }
        }
        return $this->incrementalResults;
    }

    public function getIncrementalResults()
    {
        return $this->incrementalResults;
    }

    protected function setIncrementalResults(Result $result)
    {
        $this->incrementalResults = $result;
    }

    /**
     * Run all of the tasks in a provided list, ignoring failures.
     * This is used to roll back or complete.
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
    }
}
