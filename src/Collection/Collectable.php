<?php

namespace Robo\Collection;

/**
 * The 'Collectable' trait is used by Robo\Task\BaseTask, so all Robo
 * tasks are collectable.
 *
 * Use $task->addToCollection($collection) instead of $task->run() to queue
 * up this task for execution later, when $collection->run() is executed.
 */
trait Collectable
{
    public function addToCollection(Collection $collection, $taskName = Collection::UNNAMEDTASK, TaskInterface $rollbackTask = null)
    {
        return $this->addCollectableToCollection($this, $collection, $taskName, $rollbackTask);
    }

    public function addToCollectionAndIgnoreErrors(Collection $collection, $taskName = Collection::UNNAMEDTASK)
    {
        return $this->addCollectableToCollection(new IgnoreErrorsTaskWrapper($this), $collection, $taskName);
    }

    private function addCollectableToCollection($task, Collection $collection, $taskName = Collection::UNNAMEDTASK, TaskInterface $rollbackTask = null)
    {
        $collection->addTask($taskName, $task);
        if ($rollbackTask) {
            $collection->rollback($rollbackTask);
        }
        return $this;
    }
}
