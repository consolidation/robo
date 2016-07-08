<?php
namespace Robo;

use Robo\Contract\TaskInterface;
use Robo\Common\IO;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Robo\Contract\WrappedTaskInterface;

class TaskBuilder implements ContainerAwareInterface, TaskInterface
{
    use ContainerAwareTrait;
    use LoadAllTasks;

    protected $collection;
    protected $currentTask;

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

    /**
     * Print a progress message.
     */
    public function progressMessage($text, $context = [], $level = LogLevel::NOTICE)
    {
        $this->getCollection()->progressMessage($text, $context, $level);
        return $this;
    }

    /**
     * Override TaskAccessor::builder(). By default, a new builder
     * is returned, so RoboFile::taskFoo() will create a 'foo' task
     * with its own builder.  If TaskBuilder::taskBar() is called, though,
     * then the task accessor will fetch the builder to use from this
     * method, and the new task will go into the existing builder instance.
     */
    protected function builder()
    {
        return $this;
    }

    /**
     * Add a rollback task to the builder.
     * Example: `$this->builder()->rollback($this->taskDeleteDir(...));`
     * @return type
     */
    public function rollback($task)
    {
        // Ensure that we have a collection if we are going to add
        // a rollback function.
        $this->getCollection()->rollback($task);
        return $this;
    }

    /**
     * Add a function callback as a rollback task.
     * @param callable $rollbackCode
     * @return type
     */
    public function rollbackCode(callable $rollbackCode)
    {
        $this->getCollection()->rollbackCode($rollbackCode);
        return $this;
    }

    public function addCode(callable $code)
    {
        $this->getCollection()->addCode($code);
        return $this;
    }

    /**
     * Called by the factory method of each task; adds the current
     * task to the task builder.
     */
    public function addTaskToBuilder($currentTask)
    {
        // Postpone creation of the collection until the second time
        // we are called. At that time, $this->currentTask will already
        // be populated.  We call 'getCollection()' so that it will
        // create the collection and add the current task to it.
        if (!$this->collection && $this->currentTask) {
            $this->getCollection();
        }
        $this->currentTask = $currentTask;
        if ($this->collection) {
            $this->addToCollection($currentTask);
        }
        return $this;
    }

    /**
     * Return the collection of tasks associated with this builder.
     *
     * @return Collection
     */
    protected function getCollection()
    {
        return $this->addToCollection($this->currentTask);
    }

    protected function addToCollection($task)
    {
        if (!$this->collection) {
            $this->collection = $this->collection();
        }
        if ($task) {
            $this->collection->add($task);
        }
        return $this->collection;
    }

    /**
     * Return the current task for this task builder.  Use a method
     * name that is not likely to conflict with method names in any task.
     */
    public function getTaskBuilderCurrentTask()
    {
        return $this->currentTask;
    }

    /**
     * Calling the task builder with methods of the current
     * task calls through to that method of the task.
     */
    public function __call($fn, $args)
    {
        // Calls to $this->builder()->taskFoo() cannot be made directly,
        // because all of the task methods are protected.  These calls will
        // therefore end up here.  If the method name begins with 'task',
        // then it is eligible to be used with the builder.
        if (preg_match('#^task[A-Z]#', $fn)) {
            return $this->build($fn, $args);
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
        return $this->addTaskToBuilder($task);
    }

    /**
     * When we run the task builder, run everything in the collection.
     */
    public function run()
    {
        if ($this->collection) {
            return $this->collection->run();
        }
        return $this->currentTask->run();
    }
}
