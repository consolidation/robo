<?php

namespace Robo\TaskCollection;

use Robo\Result;
use Robo\Contract\TaskInterface;
use Robo\Contract\AfterTaskInterface;

/**
 * Creates a task wrapper that serves as an adapter to convert a
 * TaskInterface task into an AfterTaskInterface.
 *
 * Clients usually do not need to use this class directly; when a
 * task is added to a task collection via the add() method, the task
 * will automatically be wrapped in a task wrapper so that it presents
 * a unified interface for the collection.
 */
class TaskWrapper implements AfterTaskInterface
{
    private $task;
    private $before = [];
    private $after = [];

    public function __construct(TaskInterface $task)
    {
        $this->task = $task;
    }

    public function before($before)
    {
        $this->before[] = static::wrap($before);
    }

    public function after($after)
    {
        $this->after[] = static::wrap($after);
    }

    public static function wrap($task)
    {
        if ($task instanceof AfterTaskInterface) {
            return $task;
        }
        if ($task instanceof \Closure) {
            return new FunctionWrapper($task);
        }
        return new TaskWrapper($task);
    }

    public function run(Result $incrementalResults)
    {
        // Run all of the 'before' tasks
        $incrementalResults = $this->runFilters($this->before, $incrementalResults);
        // Run the main task
        if ($this->task instanceof AfterTaskInterface) {
            $incrementalResults = $this->task->run($incrementalResults);
        } else {
            $result = $this->task->run();
            if ($result instanceof Result) {
                $result->merge($incrementalResults);
                $incrementalResults = $result;
            }
        }
        // Run all of the 'after' tasks
        return $this->runFilters($this->after, $incrementalResults);
    }

    protected function runFilters($filters, $incrementalResults)
    {
        foreach ($filters as $filter) {
            $incrementalResults = $filter->run($incrementalResults);
        }
        return $incrementalResults;
    }
}
