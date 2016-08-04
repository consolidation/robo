<?php
namespace Robo\Collection;

use Robo\Result;
use Psr\Log\LogLevel;
use Robo\Contract\TaskInterface;
use Robo\Container\SimpleServiceProvider;
use Robo\Task\StackBasedTask;
use Robo\Task\BaseTask;
use Robo\TaskInfo;
use Robo\Contract\WrappedTaskInterface;

use Robo\Contract\ProgressIndicatorAwareInterface;
use Robo\Common\ProgressIndicatorAwareTrait;
use Robo\Contract\InflectionInterface;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;

/**
 * Group tasks into a collection that run together. Supports
 * rollback operations for handling error conditions.
 *
 * Clients should favor using a CollectionBuilder over direct use of
 * the Collection class.  @see CollectionBuilder
 *
 * Below, the example FilesystemStack task is added to a collection,
 * and associated with a rollback task.  If any of the operations in
 * the FilesystemStack, or if any of the other tasks also added to
 * the task collection should fail, then the rollback function is
 * called. Here, taskDeleteDir is used to remove partial results
 * of an unfinished task.
 *
 * ``` php
 * <?php
 * $collection = $this->collection();
 * $collection->rollback(
 *     $this->taskDeleteDir('logs')
 * )
 * $collection->add(
 *     $this->taskFilesystemStack()
 *        ->mkdir('logs')
 *        ->touch('logs/.gitignore')
 *        ->chgrp('logs', 'www-data')
 *        ->symlink('/var/log/nginx/error.log', 'logs/error.log')
 * );
 * $collection->run();
 *
 * ?>
 * ```
 */
class Collection extends BaseTask implements CollectionInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected $taskList = [];
    protected $rollbackStack = [];
    protected $completionStack = [];
    /** var CollectionInterface */
    protected $parentCollection;

    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    public function setProgressBarAutoDisplayInterval($interval)
    {
        if (!$this->progressIndicator) {
            return;
        }
        return $this->progressIndicator->setProgressBarAutoDisplayInterval($interval);
    }

    /**
     * @inheritdoc
     */
    public function add(TaskInterface $task, $name = self::UNNAMEDTASK)
    {
        $task = new CompletionWrapper($this, $task);
        $this->addToTaskList($name, $task);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addCode(callable $code, $name = self::UNNAMEDTASK)
    {
        return $this->add(new CallableTask($code, $this), $name);
    }

    /**
     * @inheritdoc
     */
    public function addIterable($iterable, callable $code)
    {
        $callbackTask = (new IterationTask($iterable, $code, $this))->inflect($this);
        return $this->add($callbackTask);
    }

    /**
     * @inheritdoc
     */
    public function rollback(TaskInterface $rollbackTask)
    {
        // Rollback tasks always try as hard as they can, and never report failures.
        $rollbackTask = $this->ignoreErrorsTaskWrapper($rollbackTask);
        return $this->wrapAndRegisterRollback($rollbackTask);
    }

    /**
     * @inheritdoc
     */
    public function rollbackCode(callable $rollbackCode)
    {
        // Rollback tasks always try as hard as they can, and never report failures.
        $rollbackTask = $this->ignoreErrorsCodeWrapper($rollbackCode);
        return $this->wrapAndRegisterRollback($rollbackTask);
    }

    /**
     * @inheritdoc
     */
    public function completion(TaskInterface $completionTask)
    {
        $collection = $this;
        $completionRegistrationTask = new CallableTask(
            function () use ($collection, $completionTask) {

                $collection->registerCompletion($completionTask);
            },
            $this
        );
        $this->addToTaskList(self::UNNAMEDTASK, $completionRegistrationTask);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function completionCode(callable $completionTask)
    {
        $completionTask = new CallableTask($completionTask, $this);
        return $this->completion($completionTask);
    }

    /**
     * @inheritdoc
     */
    public function before($name, $task, $nameOfTaskToAdd = self::UNNAMEDTASK)
    {
        return $this->addBeforeOrAfter(__FUNCTION__, $name, $task, $nameOfTaskToAdd);
    }

    /**
     * @inheritdoc
     */
    public function after($name, $task, $nameOfTaskToAdd = self::UNNAMEDTASK)
    {
        return $this->addBeforeOrAfter(__FUNCTION__, $name, $task, $nameOfTaskToAdd);
    }

    /**
     * @inheritdoc
     */
    public function progressMessage($text, $context = [], $filter = false, $level = LogLevel::NOTICE)
    {
        $context += ['name' => 'Progress'];
        $context += TaskInfo::getTaskContext($this);
        return $this->addCode(
            function () use ($level, $text, $context, $filter) {
                $filteredContext = $filter ? $filter($context, $this) : $context;
                $this->printTaskOutput($level, $text, $filteredContext);
            }
        );
    }

    protected function wrapAndRegisterRollback(TaskInterface $rollbackTask)
    {
        $collection = $this;
        $rollbackRegistrationTask = new CallableTask(
            function () use ($collection, $rollbackTask) {
                $collection->registerRollback($rollbackTask);
            },
            $this
        );
        $this->addToTaskList(self::UNNAMEDTASK, $rollbackRegistrationTask);
        return $this;
    }

    /**
     * Add either a 'before' or 'after' function or task.
     */
    protected function addBeforeOrAfter($method, $name, $task, $nameOfTaskToAdd)
    {
        if (is_callable($task)) {
            $task = new CallableTask($task, $this);
        }
        $existingTask = $this->namedTask($name);
        $fn = [$existingTask, $method];
        call_user_func($fn, $task, $nameOfTaskToAdd);
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
    public function ignoreErrorsTaskWrapper(TaskInterface $task)
    {
        // If the task is a stack-based task, then tell it
        // to try to run all of its operations, even if some
        // of them fail.
        if ($task instanceof StackBasedTask) {
            $task->stopOnFail(false);
        }
        $ignoreErrorsInTask = function () use ($task) {
            $data = [];
            try {
                $result = $this->runSubtask($task);
                $message = $result->getMessage();
                $data = $result->getData();
                $data['exitcode'] = $result->getExitCode();
            } catch (\Exception $e) {
                $message = $e->getMessage();
            }

            return Result::success($task, $message, $data);
        };
        // Wrap our ignore errors callable in a task.
        return new CallableTask($ignoreErrorsInTask, $this);
    }

    public function ignoreErrorsCodeWrapper(callable $task)
    {
        return $this->ignoreErrorsTaskWrapper(new CallableTask($task, $this));
    }

    /**
     * Return the list of task names added to this collection.
     */
    public function taskNames()
    {
        return array_keys($this->taskList);
    }

    /**
     * Test to see if a specified task name exists.
     * n.b. before() and after() require that the named
     * task exist; use this function to test first, if
     * unsure.
     */
    public function hasTask($name)
    {
        return array_key_exists($name, $this->taskList);
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
     * @return Element
     *   The task group for the named task. Generally this is only
     *   used to call 'before()' and 'after()'.
     */
    protected function namedTask($name)
    {
        if (!$this->hasTask($name)) {
            throw new \RuntimeException("Could not find task named $name");
        }
        return $this->taskList[$name];
    }

    /**
     * Add a list of tasks to our task collection.
     *
     * @param TaskInterface[]
     *   An array of tasks to run with rollback protection
     */
    public function addTaskList(array $tasks)
    {
        foreach ($tasks as $name => $task) {
            $this->add($task, $name);
        }
        return $this;
    }

    /**
     * Add the provided task to our task list.
     */
    protected function addToTaskList($name, TaskInterface $task)
    {
        // All tasks are stored in a task group so that we have a place
        // to hang 'before' and 'after' tasks.
        $taskGroup = new Element($task);
        // If a task name is not provided, then we'll let php pick
        // the array index.
        if (static::isUnnamedTask($name)) {
            $this->taskList[] = $taskGroup;
            return $this;
        }
        // If we are replacing an existing task with the
        // same name, ensure that our new task is added to
        // the end.
        $this->taskList[$name] = $taskGroup;
        return $this;
    }

    /**
     * Set the parent collection. This is necessary so that nested
     * collections' rollback and completion tasks can be added to the
     * top-level collection, ensuring that the rollbacks for a collection
     * will run if any later task fails.
     */
    public function setParentCollection(NestedCollectionInterface $parentCollection)
    {
        $this->parentCollection = $parentCollection;
        return $this;
    }

    /**
     * Get the appropriate parent collection to use
     * @return CollectionInterface
     */
    public function getParentCollection()
    {
        return $this->parentCollection ? $this->parentCollection : $this;
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
    public function registerRollback(TaskInterface $rollbackTask)
    {
        if ($this->parentCollection) {
            return $this->parentCollection->registerRollback($rollbackTask);
        }
        if ($rollbackTask) {
            $this->rollbackStack[] = $rollbackTask;
        }
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
    public function registerCompletion(TaskInterface $completionTask)
    {
        if ($this->parentCollection) {
            return $this->parentCollection->registerCompletion($completionTask);
        }
        if ($completionTask) {
            // Completion tasks always try as hard as they can, and never report failures.
            $completionTask = $this->ignoreErrorsTaskWrapper($completionTask);
            $this->completionStack[] = $completionTask;
        }
    }

    /**
     * Return the count of steps in this collection
     * @return int
     */
    public function progressIndicatorSteps()
    {
        $steps = 0;
        foreach ($this->taskList as $name => $taskGroup) {
            foreach ($taskGroup->getTaskList() as $task) {
                if ($task instanceof WrappedTaskInterface) {
                    $task = $task->original();
                }
                // If the task is a ProgressIndicatorAwareInterface, then it
                // will advance the progress indicator a number of times.
                if ($task instanceof ProgressIndicatorAwareInterface) {
                    $steps += $task->progressIndicatorSteps();
                }
                // We also advance the progress indicator once regardless
                // of whether it is progress-indicator aware or not.
                $steps++;
            }
        }
        return $steps;
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

    private function runWithoutCompletion()
    {
        $result = Result::success($this);

        if (empty($this->taskList)) {
            return $result;
        }

        $this->startProgressIndicator();
        if ($result->wasSuccessful()) {
            foreach ($this->taskList as $name => $taskGroup) {
                $taskList = $taskGroup->getTaskList();
                $result = $this->runTaskList($name, $taskList, $result);
                if (!$result->wasSuccessful()) {
                    $this->fail();
                    return $result;
                }
            }
            $this->taskList = [];
        }
        $this->stopProgressIndicator();
        $result['time'] = $this->getExecutionTime();

        return $result;
    }

    /**
     * Run every task in a list, but only up to the first failure.
     * Return the failing result, or success if all tasks run.
     */
    private function runTaskList($name, array $taskList, $result)
    {
        try {
            foreach ($taskList as $taskName => $task) {
                $taskResult = $this->runSubtask($task);
                $this->advanceProgressIndicator();
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
        } catch (\Exception $e) {
            // Tasks typically should not throw, but if one does, we will
            // convert it into an error and roll back.
            return Result::fromException($task, $e, $result->getData());
        }
        return $result;
    }

    /**
     * Force the rollback functions to run
     */
    public function fail()
    {
        $this->disableProgressIndicator();
        $this->runRollbackTasks();
        $this->complete();
        return $this;
    }

    /**
     * Force the completion functions to run
     */
    public function complete()
    {
        $this->detatchProgressIndicator();
        $this->runTaskListIgnoringFailures($this->completionStack);
        $this->reset();
        return $this;
    }

    /**
     * Reset this collection, removing all tasks.
     */
    public function reset()
    {
        $this->taskList = [];
        $this->completionStack = [];
        $this->rollbackStack = [];
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

    protected function runSubtask($task)
    {
        $original = ($task instanceof WrappedTaskInterface) ? $task->original() : $task;
        $this->setParentCollectionForTask($original, $this->getParentCollection());
        if ($original instanceof InflectionInterface) {
            $original->inflect($this);
        }
        $taskResult = $task->run();
        return $taskResult;
    }

    protected function setParentCollectionForTask($task, $parentCollection)
    {
        if ($task instanceof NestedCollectionInterface) {
            $task->setParentCollection($parentCollection);
        }
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
    protected function runTaskListIgnoringFailures(array $taskList)
    {
        foreach ($taskList as $task) {
            try {
                $this->runSubtask($task);
            } catch (\Exception $e) {
                // Ignore rollback failures.
            }
        }
    }
}
