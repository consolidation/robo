<?php
namespace Robo\TaskCollection;

/**
 * The 'Collectable' trait is used by Robo\Task\BaseTask, so all Robo
 * tasks are collectable.
 *
 * Use $task->collect($collection) instead of $task->run() to queue
 * up this task for execution later, when $collection->run() is executed.
 */
trait Collectable
{
    public function collect(Collection $collection, TaskInterface $rollbackTask = NULL) {
        $collection->add($this, $rollbackTask);
        return $this;
    }

    public function collectAndIgnoreErrors(Collection $collection) {
        $collection->addAndIgnoreErrors($this);
        return $this;
    }
}
