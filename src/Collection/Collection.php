<?php
namespace Robo\Collection;

use Robo\Result;
use Robo\Contract\TaskInterface;

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
    protected $previousResult;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->previousResult = Result::success($this);
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
        // If '$name' was unspecified, then the single parameter provided
        // is the task or Callable object.  Make $name 'UNNAMEDTASK'.
        if (!is_string($name) && ($task == null)) {
            $task = $name;
            $name = self::UNNAMEDTASK;
        }
        // If $task is an array (and isn't a Callable), then add every item
        // in the array individually.
        if (!is_callable($task) && is_array($task)) {
            return $this->addTaskList($task);
        }
        // Otherwise, add the named (or unnamed) task.
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
        $rollbackTask = $this->wrapTask($rollbackTask);
        $collection = $this;
        $rollbackRegistrationTask = $this->wrapTask(function () use ($collection, $rollbackTask) {
            $collection->registerRollback($rollbackTask);
        });
        $this->addToTaskStack(self::UNNAMEDTASK, $rollbackRegistrationTask);
        return $this;
    }

    /**
     * Add a completion task to our task collection.  A completion task
     * will execute EITHER after all tasks succeed, OR immediatley after
     * any task fails.  Completion tasks never cause errors to be returned
     * from Collection::run(), even if they fail.
     *
     * @param TaskInterface
     *   The completion task to add.  Note that the 'run()' method of the
     *   task executes, just as if the task was added normally.
     */
    public function completion($completionTask)
    {
        // Wrap the task as necessary.
        $completionTask = $this->wrapTask($completionTask);
        $collection = $this;
        $completionRegistrationTask = $this->wrapTask(function () use ($collection, $completionTask) {
            $collection->registerCompletion($completionTask);
        });
        $this->addToTaskStack(self::UNNAMEDTASK, $completionRegistrationTask);
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
        $task = $this->wrapTask($task);
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
        $task = $this->wrapTask($task);
        $existingTask = $this->namedTask($name);
        $existingTask->after($task, $nameOfTaskToAdd);
        return $this;
    }

    /**
     * Wrap the provided task in a wrapper that will ignore
     * any errors or exceptions that may be produced.  This
     * is useful, for example, in adding optional cleanup tasks
     * at the beginning of a task collection, to remove previous
     * results which may or may not exist.
     *
     * TODO: Provide some way to specify which sort of errors
     * are ignored, so that 'file not found' may be ignored,
     * but 'permission denied' reported?
     */
    public function ignoreErrorsTaskWrapper($task)
    {
        $task = $this->wrapTask($task);
        return function () use ($task) {
            $data = [];
            try {
                $result = $task->run();
                $message = $result->getMessage();
                $data = $result->getData();
                $data['exitcode'] = $result->getExitCode();
            } catch (Exception $e) {
                $message = $e->getMessage();
            }

            return Result::success($task, $message, $data);
        };
    }

    /**
     * Return the list of task names added to this collection.
     */
    public function taskNames()
    {
        return array_keys($this->taskStack);
    }

    /**
     * Test to see if a specified task name exists.
     * n.b. before() and after() require that the named
     * task exist; use this function to test first, if
     * unsure.
     */
    public function hasTask($name)
    {
        return array_key_exists($name, $this->taskStack);
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

    /**
     * Add a list of tasks to our task collection. This is
     * protected because clients should just call 'add()'.
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
     * the task will also be executed, if the task implements
     * RollbackInterface.  addTask is protected because clients should
     * just call 'add()'.
     *
     * @param string
     *   A name for the task, used for positioning before and after tasks.
     * @param TaskInterface
     *   The task to run
     */
    protected function addTask($name, $task)
    {
        // Wrap the task as necessary.
        $task = $this->wrapTask($task);
        $this->addToTaskStack($name, new TaskWrapper($this, $task));
        return $this;
    }

    /**
     * If the task needs to be wrapped, create whatever wrapper objects are
     * needed for it.
     */
    protected function wrapTask($task)
    {
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
     * Register a rollback task to run if there is any failure.
     *
     * Clients are free to add tasks to the rollback stack as
     * desired; however, usually it is preferable to call
     * Collection::rollback() instead.  With that function,
     * the rollback function will only be called if all of the
     * tasks added before it complete successfully, AND some later
     * task fails.
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
        $rollbackTask = $this->wrapTask($rollbackTask);
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
     * temporary objects (e.g. temporary folders).  The preferred
     * way to do that, though, is to use Temporary::wrap().
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
        $completionTask = $this->wrapTask($completionTask);
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
        $result = $this->runWithoutCompletion();
        $this->complete();
        return $result;
    }

    /**
     * Like 'run()', but does not call complete().
     * Allows caller to continue adding tasks to the
     * same collection, e.g. perhaps to re-use a temporary
     * directory or other temporary which will persist
     * until 'run()' or 'complete()' is called.
     */
    public function runWithoutCompletion()
    {
        // If there were some tasks that were run before, and they
        // failed, subsequent calls to run() will do nothing further,
        // and will continue to return the same error result.
        $result = $this->previousResult;
        if ($result->wasSuccessful()) {
            foreach ($this->taskStack as $name => $taskGroup) {
                $taskList = $taskGroup->getTaskList();
                $result = $this->runTaskList($name, $taskList, $result);
                if (!$result->wasSuccessful()) {
                    $this->fail();
                    return $result;
                }
            }
            $this->taskStack = [];
        }
        $this->previousResult = $result;
        return $result;
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
    protected function runTaskList($name, $taskList, $result)
    {
        try {
            foreach ($taskList as $taskName => $task) {
                $taskResult = $task->run();
                // If the current task returns an error code, then stop
                // execution and signal a rollback.
                if (!$taskResult->wasSuccessful()) {
                    return $taskResult;
                }
                // We accumulate our results into a field so that tasks that
                // have a reference to the collection may examine and modify
                // the incremental results, if they wish.
                $key = static::isUnnamedTask($taskName) ? $name : $taskName;
                $result = $this->accumulateResults($key, $result, $taskResult);
            }
        } catch (Exception $e) {
            // Tasks typically should not throw, but if one does, we will
            // convert it into an error and roll back.
            // TODO: should we re-throw it again instead?
            return new Result($this, -1, $e->getMessage(), $result->getData());
        }
        return $result;
    }

    /**
     * Add the results from the most recent task to the accumulated
     * results from all tasks that have run so far, merging data
     * as necessary.
     */
    public function accumulateResults($key, Result $result, Result $taskResult)
    {
        // If the result is not set or is not a Result, then ignore it
        if (isset($result) && ($result instanceof Result)) {
            // If the task is unnamed, then all of its data elements
            // just get merged in at the top-level of the final Result object.
            if (static::isUnnamedTask($key)) {
                $result->merge($taskResult);
            } elseif (isset($result[$key])) {
                // There can only be one task with a given name; however, if
                // there are tasks added 'before' or 'after' the named task,
                // then the results from these will be stored under the same
                // name unless they are given a name of their own when added.
                $current = $result[$key];
                $result[$key] = $taskResult->merge($current);
            } else {
                $result[$key] = $taskResult;
            }
        }
        return $result;
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
