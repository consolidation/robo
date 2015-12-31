<?php
namespace Robo\TaskCollection;

use Robo\Result;
use Robo\Contract\TaskInterface;
use Robo\Contract\RollbackInterface;
use Robo\Contract\CollectionAwareTaskInterface;

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
 *      ->collectWithRollback($collection, $this->taskDeleteDir('logs'));
 * /// ... collect other tasks
 * $collection->run();
 *
 * ?>
 * ```
 */class Collection implements TaskInterface {

    protected $taskStack = [];
    protected $rollbackStack = [];
    protected $completionStack = [];

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
     * Add a list of tasks to our task collection.
     *
     * @param TaskInterface
     *   The task to run with rollback protection
     */
    public function addTasks($tasks) {
        foreach ($tasks as $task) {
            $this->addTask($task);
        }
    }

    /**
     * Add a task to our task collection.  If the task implements the
     * RollbackInterface, then it will be rolled back on any failure.
     *
     * @param TaskInterface
     *   The task to run with rollback protection
     */
    public function add(TaskInterface $task) {
        if ($task instanceof RollbackInterface) {
            $this->addWithRollback($task, new RollbackTask($task));
        }
        else {
            $this->addToTaskStack($task);
        }
    }

    /**
     * Add a task to our task collection.  If there is a later failure,
     * then run the provided rollback operation.  The rollback() method in
     * $task will be ignored, even if implemented.
     *
     * Note that while it is possible to nest collections of tasks, it
     * is not possible to remove rollback protection from a collection.
     * If you attempt to pass a task collection to addWithRollback(),
     * then BOTH the provided rollback task AND all of the rollback tasks
     * in the provided collection will run if there is a failure.  In this
     * instance, the nested collection's rollback functions will run prior
     * to the provided rollback task.
     *
     * @param TaskInterface
     *   The task to run
     * @param TaskInterface
     *   The rollback function to run if any command in the collection fails
     */
    public function addWithRollback(TaskInterface $task, TaskInterface $rollbackTask) {
        $this->addToTaskStack(new RollbackController($this, $task, $rollbackTask));
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
     * Try to run our tasks.  If any of them fail, then run all
     * of our rollback tasks.
     */
    public function run() {
        $result = new Result($this, 0);
        try {
            foreach ($this->taskStack as $task) {
                $taskResult = $this->call($task);
                // If the current task returns an error code, then stop
                // execution and signal a rollback.
                if (($taskResult instanceof Result) && ($taskResult->getExitCode())) {
                    $result = $taskResult;
                    break;
                }
            }
        }
        catch(Exception $e) {
            // Tasks typically do not throw, but if one does, we will
            // convert it into an error and roll back.
            // TODO: should we re-throw it again instead?
            $result = new Result($this, -1, $e->getMessage());
        }
        if ($result->getExitCode()) {
            $this->unwindRollbackStack();
        }
        $this->runCompletionStack();
        return $result;
    }

    /**
     * Call the run() method of one task
     */
    protected function call($task) {
        if ($task instanceof CollectionAwareTaskInterface) {
            return $task->runInCollection($this);
        }
        else {
            return $task->run();
        }
    }

    /**
     * Force the rollback functions to run
     */
    public function fail() {
        $this->unwindRollbackStack();
        $this->runCompletionStack();
    }

    /**
     * Run all of our rollback tasks.
     *
     * Note that Collection does not implement RollbackInterface, but
     * it may still be used as a task inside another task collection
     * (i.e. you can nest task collections, if desired).
     */
    protected function unwindRollbackStack() {
        static::runTaskListIgnoringFailures($this->rollbackStack);
        // Erase our rollback stack once we have finished rolling
        // everything back.  This will allow us to potentially use
        // a command collection more than once (e.g. to retry a
        // failed operation after doing some error recovery).
        $this->rollbackStack = [];
    }

    /**
     * Run all of our completion tasks.
     */
    protected function runCompletionStack() {
        static::runTaskListIgnoringFailures($this->completionStack);
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
