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
 *      ->collect($collection, $this->taskDeleteDir('logs'));
 * /// ... collect other tasks
 * $collection->run();
 *
 * ?>
 * ```
 */
class Collection implements TaskInterface {

    protected $taskStack = [];
    protected $rollbackStack = [];
    protected $completionStack = [];

    /**
     * Add a list of tasks to our task collection.
     *
     * @param TaskInterface
     *   The task to run with rollback protection
     */
    public function addTasks($tasks) {
        foreach ($tasks as $task) {
            $this->add($task);
        }
    }

    /**
     * Add a task to our task collection.  If there is a later failure,
     * then run the provided rollback operation.  The rollback() method of
     * the task will also be executed, if the task implements RollbackInterface.
     *
     * @param TaskInterface
     *   The task to run
     * @param TaskInterface
     *   The rollback function to run if any command in the collection fails
     */
    public function add(TaskInterface $task, TaskInterface $rollbackTask = NULL) {
        $this->addToTaskStack(new CollectionTask($this, $task, $rollbackTask));
    }

    /**
     * Add a task to our task stack; when it runs, ignore any errors that
     * it may generate.
     *
     * @param TaskInterface
     *   The task to run
     */
    public function addAndIgnoreErrors(TaskInterface $task) {
        $this->addToTaskStack(new IgnoreErrorsTaskWrapper($task));
    }

    /**
     * Add the provided task to our task list.
     */
    protected function addToTaskStack(TaskInterface $task) {
        $this->taskStack[] = $task;
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
    public function registerRollback(TaskInterface $rollbackTask) {
        $this->rollbackStack[] = $rollbackTask;
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
    public function registerCompletion(TaskInterface $completionTask) {
        $this->completionStack[] = $completionTask;
    }

    /**
     * Try to run our tasks.  If any of them fail, then run all
     * of our rollback tasks.
     */
    public function run() {
        $result = $this->runTaskList($this->taskStack);
        if (!$result->wasSuccessful()) {
            $this->runRollbackTasks();
        }
        $this->complete();
        return $result;
    }

    /**
     * Register a task for rollback and completion handling, but
     * do NOT collect it for execution.
     *
     * This usually happens automatically, via CollectionTask
     */
    public function register($task) {
        if ($task instanceof RollbackInterface) {
            $this->registerRollback(new RollbackTask($task));
        }
        if ($task instanceof CompletionInterface) {
            $this->registerCompletion(new CompletionTask($task));
        }
    }

    /**
     * Force the rollback functions to run
     */
    public function fail() {
        $this->runRollbackTasks();
        $this->complete();
    }

    /**
     * Force the completion functions to run
     */
    public function complete() {
        static::runTaskListIgnoringFailures($this->completionStack);
    }

    /**
     * Reset this collection, removing all tasks.
     */
    public function reset() {
        $this->taskStack = [];
        $this->completionStack = [];
        $this->rollbackStack = [];
    }

    /**
     * Run all of our rollback tasks.
     *
     * Note that Collection does not implement RollbackInterface, but
     * it may still be used as a task inside another task collection
     * (i.e. you can nest task collections, if desired).
     */
    protected function runRollbackTasks() {
        static::runTaskListIgnoringFailures($this->rollbackStack);
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
    public function runTaskList($taskList) {
        try {
            foreach ($taskList as $task) {
                $result = $task->run();
                // If the current task returns an error code, then stop
                // execution and signal a rollback.
                if (($result instanceof Result) && (!$result->wasSuccessful())) {
                    return $result;
                }
            }
        }
        catch(Exception $e) {
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
    protected static function runTaskListIgnoringFailures($taskList) {
        foreach ($taskList as $task) {
            try {
                $task->run();
            }
            catch (Exception $e) {
                // Ignore rollback failures.
            }
        }
    }
}
