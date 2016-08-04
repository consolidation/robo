<?php
namespace Robo\Collection;

use Robo\Config;
use Robo\Common\IO;
use Robo\Common\Timer;
use Psr\Log\LogLevel;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Robo\Contract\TaskInterface;
use Robo\Contract\CompletionInterface;
use Robo\Contract\WrappedTaskInterface;
use Robo\Collection\NestedCollectionInterface;
use Robo\LoadAllTasks;
use Robo\Task\Simulator;
use Robo\Collection\CompletionWrapper;
use Robo\Collection\Temporary;
use Robo\Contract\ConfigAwareInterface;
use Robo\Common\ConfigAwareTrait;

/**
 * Creates a collection, and adds tasks to it.  The collection builder
 * offers a streamlined chained-initialization mechanism for easily
 * creating task groups.  Facilities for creating working and temporary
 * directories are also provided.
 *
 * ``` php
 * <?php
 * $result = $this->collectionBuilder()
 *   ->taskFilesystemStack()
 *     ->mkdir('g')
 *     ->touch('g/g.txt')
 *   ->rollback(
 *     $this->taskDeleteDir('g')
 *   )
 *   ->taskFilesystemStack()
 *     ->mkdir('g/h')
 *     ->touch('g/h/h.txt')
 *   ->taskFilesystemStack()
 *     ->mkdir('g/h/i/c')
 *     ->touch('g/h/i/i.txt')
 *   ->run()
 * ?>
 *
 * In the example above, the `taskDeleteDir` will be called if
 * ```
 */
class CollectionBuilder implements NestedCollectionInterface, ConfigAwareInterface, ContainerAwareInterface, TaskInterface, WrappedTaskInterface
{
    use ConfigAwareTrait;
    use ContainerAwareTrait;
    use LoadAllTasks;
    use Timer;

    protected $collection;
    protected $currentTask;
    protected $simulated;

    public function __construct()
    {
    }

    public function simulated($simulated = true)
    {
        $this->simulated = $simulated;
    }

    public function isSimulated()
    {
        if (!isset($this->simulated)) {
            $this->simulated = $this->getConfig()->get(Config::SIMULATE);
        }
        return $this->simulated;
    }

    /**
     * Create a temporary directory to work in. When the collection
     * completes or rolls back, the temporary directory will be deleted.
     * Returns the path to the location where the directory will be
     * created.
     *
     * @return string
     */
    public function tmpDir($prefix = 'tmp', $base = '', $includeRandomPart = true)
    {
        // n.b. Any task that the builder is asked to create is
        // automatically added to the builder's collection, and
        // wrapped in the builder object. Therefore, the result
        // of any call to `taskFoo()` from within the builder will
        // always be `$this`.
        return $this->taskTmpDir($prefix, $base, $includeRandomPart)->getPath();
    }

    /**
     * Create a working directory to hold results. A temporary directory
     * is first created to hold the intermediate results.  After the
     * builder finishes, the work directory is moved into its final location;
     * any results already in place will be moved out of the way and
     * then deleted.
     *
     * @param string $finalDestination The path where the working directory
     *   will be moved once the task collection completes.
     * @return string
     */
    public function workDir($finalDestination)
    {
        // Creating the work dir task in this context adds it to our task collection.
        return $this->taskWorkDir($finalDestination)->getPath();
    }

    public function addTask(TaskInterface $task)
    {
        $this->getCollection()->add($task);
        return $this;
    }

    public function addCode(callable $code)
    {
        $this->getCollection()->addCode($code);
        return $this;
    }

    /**
     * Add a list of tasks to our task collection.
     *
     * @param TaskInterface[]
     *   An array of tasks to run with rollback protection
     */
    public function addTaskList(array $tasks)
    {
        $this->getCollection()->addTaskList($tasks);
        return $this;
    }

    public function rollback(TaskInterface $task)
    {
        // Ensure that we have a collection if we are going to add
        // a rollback function.
        $this->getCollection()->rollback($task);
        return $this;
    }

    public function rollbackCode(callable $rollbackCode)
    {
        $this->getCollection()->rollbackCode($rollbackCode);
        return $this;
    }

    public function completion(TaskInterface $task)
    {
        $this->getCollection()->completion($task);
        return $this;
    }

    public function completionCode(callable $completionCode)
    {
        $this->getCollection()->completionCode($completionCode);
        return $this;
    }

    public function progressMessage($text, $context = [], $filter = false, $level = LogLevel::NOTICE)
    {
        $this->getCollection()->progressMessage($text, $context, $filter, $level);
        return $this;
    }

    public function setParentCollection(NestedCollectionInterface $parentCollection)
    {
        $this->getCollection()->setParentCollection($parentCollection);
        return $this;
    }

    /**
     * Called by the factory method of each task; adds the current
     * task to the task builder.
     *
     * TODO: protected
     */
    public function addTaskToCollection($task)
    {
        // Postpone creation of the collection until the second time
        // we are called. At that time, $this->currentTask will already
        // be populated.  We call 'getCollection()' so that it will
        // create the collection and add the current task to it.
        // Note, however, that if our only tasks implements NestedCollectionInterface,
        // then we should force this builder to use a collection.
        if (!$this->collection && (isset($this->currentTask) || ($task instanceof NestedCollectionInterface))) {
            $this->getCollection();
        }
        $this->currentTask = $task;
        if ($this->collection) {
            $this->collection->add($task);
        }
        return $this;
    }

    /**
     * Return the current task for this collection builder.
     * TODO: Not needed?
     */
    public function getCollectionBuilderCurrentTask()
    {
        return $this->currentTask;
    }

    /**
     * Calling the task builder with methods of the current
     * task calls through to that method of the task.
     */
    public function __call($fn, $args)
    {
        // Calls to $this->collectionBuilder()->taskFoo() cannot be made
        // directly because all of the task methods are protected.  These
        // calls will therefore end up here.  If the method name begins
        // with 'task', then it is eligible to be used with the builder.
        if (preg_match('#^task[A-Z]#', $fn)) {
            return $this->build($fn, $args);
        }
        if (!isset($this->currentTask)) {
            throw new \BadMethodCallException("No such method $fn: current task undefined in collection builder.");
        }
        // If the method called is a method of the current task,
        // then call through to the current task's setter method.
        $result = call_user_func_array([$this->currentTask, $fn], $args);

        // If something other than a setter method is called,
        // then return its result.
        if (isset($result) && ($result !== $this->currentTask)) {
            return $result;
        }

        return $this;
    }

    /**
     * Construct the desired task via the container and add it to this builder.
     */
    public function build($name, $args)
    {
        $task = $this->getContainer()->get($name, $args);
        if (!$task) {
            throw new RuntimeException("Can not construct task $name");
        }
        $task = $this->fixTask($task, $args);
        return $this->addTaskToCollection($task);
    }

    protected function fixTask($service, $args)
    {
        // Do not wrap our wrappers.
        if ($service instanceof CompletionWrapper || $service instanceof Simulator) {
            return $service;
        }

        // Remember whether or not this is a task before
        // it gets wrapped in any service decorator.
        $isTask = $service instanceof TaskInterface;
        $isCollection = $service instanceof NestedCollectionInterface;

        // If the task implements CompletionInterface, ensure
        // that its 'complete' method is called when the application
        // terminates -- but only if its 'run' method is called
        // first.  If the task is added to a collection, then the
        // task will be unwrapped via its `original` method, and
        // it will be re-wrapped with a new completion wrapper for
        // its new collection.
        if ($service instanceof CompletionInterface) {
            $service = new CompletionWrapper(Temporary::getCollection(), $service);
        }

        // If we are in simulated mode, then wrap any task in
        // a TaskSimulator.
        if ($isTask && !$isCollection && ($this->isSimulated())) {
            $service = $this->getContainer()->get('simulator', [$service, $args]);
        }

        return $service;
    }

    /**
     * When we run the collection builder, run everything in the collection.
     */
    public function run()
    {
        $this->startTimer();
        $result = $this->runTasks();
        $this->stopTimer();
        $result['time'] = $this->getExecutionTime();
        return $result;
    }

    /**
     * If there is a single task, run it; if there is a collection, run
     * all of its tasks.
     */
    protected function runTasks()
    {
        if (!$this->collection && $this->currentTask) {
            return $this->currentTask->run();
        }
        return $this->getCollection()->run();
    }

    public function original()
    {
        return $this->getCollection();
    }

    /**
     * Return the collection of tasks associated with this builder.
     *
     * @return Collection
     */
    public function getCollection()
    {
        if (!isset($this->collection)) {
            $this->collection = $this->collection();
            $this->collection->setProgressBarAutoDisplayInterval($this->getConfig()->get(Config::PROGRESS_BAR_AUTO_DISPLAY_INTERVAL));

            if (isset($this->currentTask)) {
                $this->collection->add($this->currentTask);
            }
        }
        return $this->collection;
    }

    /**
     * Override TaskAccessor::collectionBuilder(). By default, a new builder
     * is returned, so RoboFile::taskFoo() will create a 'foo' task
     * with its own builder.  If CollectionBuilder::taskBar() is called, though,
     * then the task accessor will fetch the builder to use from this
     * method, and the new task will go into the existing builder instance.
     */
    protected function collectionBuilder()
    {
        return $this;
    }
}
