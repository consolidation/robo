<?php

namespace Robo\Collection;

use Robo\Contract\TaskInterface;
use Robo\Contract\WrappedTaskInterface;
use Robo\Contract\ProgressIndicatorAwareInterface;

/**
 * One element in a collection.  Each element consists of a task
 * all of its before tasks, and all of its after tasks.
 *
 * This class is internal to Collection; it should not be used directly.
 */
class Element
{
    /**
     * @var \Robo\Contract\TaskInterface
     */
    protected $task;

    /**
     * @var array
     */
    protected $before = [];

    /**
     * @var array
     */
    protected $after = [];

    public function __construct(TaskInterface $task)
    {
        $this->task = $task;
    }

    /**
     * @param mixed $before
     * @param string $name
     */
    public function before($before, $name)
    {
        if ($name) {
            $this->before[$name] = $before;
        } else {
            $this->before[] = $before;
        }
    }

    /**
     * @param mixed $after
     * @param string $name
     */
    public function after($after, $name)
    {
        if ($name) {
            $this->after[$name] = $after;
        } else {
            $this->after[] = $after;
        }
    }

    /**
     * @return array
     */
    public function getBefore()
    {
        return $this->before;
    }

    /**
     * @return array
     */
    public function getAfter()
    {
        return $this->after;
    }

    /**
     * @return \Robo\Contract\TaskInterface
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @return array
     */
    public function getTaskList()
    {
        return array_merge($this->getBefore(), [$this->getTask()], $this->getAfter());
    }

    /**
     * @return int
     */
    public function progressIndicatorSteps()
    {
        $steps = 0;
        foreach ($this->getTaskList() as $task) {
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
        return $steps;
    }
}
