<?php

namespace Robo\Collection;

use Robo\Result;
use Robo\Contract\TaskInterface;

/**
 * One element in a collection.  Each element consists of a task
 * all of its before tasks, and all of its after tasks.
 *
 * This class is internal to Collection; it should not be used directly.
 */
class Element
{
    protected $task;
    protected $before = [];
    protected $after = [];

    public function __construct(TaskInterface $task)
    {
        $this->task = $task;
    }

    public function before($before, $name)
    {
        if ($name) {
            $this->before[$name] = $before;
        } else {
            $this->before[] = $before;
        }
    }

    public function after($after, $name)
    {
        if ($name) {
            $this->after[$name] = $after;
        } else {
            $this->after[] = $after;
        }
    }

    public function getBefore()
    {
        return $this->before;
    }

    public function getAfter()
    {
        return $this->after;
    }

    public function getTask()
    {
        return $this->task;
    }

    public function getTaskList()
    {
        return array_merge($this->before, [$this->task], $this->after);
    }
}
