<?php
namespace Robo\Contract;

use Robo\Collection\Collection;

/**
 * A Robo CollectedInterface is a mixin interface implemented
 * by tasks that wish to examine or modify the intermediate
 * results durring the execution of a Collection of tasks.  The
 * collection will notify all tasks that implement CollectedInterface
 * by calling `collected`.  The task may at that time save a reference
 * to the collection.  It must return either $this, or a wrapper object.
 * It will be the function result that is executed.
 *
 * Interface CollectedInterface
 * @package Robo\Contract
 */
interface CollectedInterface
{
    /**
     * Tasks should implement CollectedInterface if they wish to
     * be notified when they are added to a collection.  Tasks
     * are given a reference to their collection when added.
     * This method must return $this, or an object that wraps $this.
     * @param Collection $collection The collection this task is being added to
     * @return \Robo\Contrac\TaskInterface
     */
    public function collected(Collection $collection);
}
