<?php

namespace Robo\TaskCollection;

/**
 * The 'Collectable' trait is used by Robo\Task\BaseTask, so all Robo
 * tasks are collectable.
 *
 * Use $task->runLater($collection) instead of $task->run() to queue
 * up this task for execution later, when $collection->run() is executed.
 */
trait Collectable
{
    public function runLater(Collection $collection, TaskInterface $rollbackTask = null)
    {
        $collection->addTask(Collection::UNNAMEDTASK, $this, $rollbackTask);

        return $this;
    }

    public function runLaterAndIgnoreErrors(Collection $collection)
    {
        $collection->addAndIgnoreErrors(Collection::UNNAMEDTASK, $this);

        return $this;
    }
}
