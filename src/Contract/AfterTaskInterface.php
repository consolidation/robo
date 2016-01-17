<?php
namespace Robo\Contract;

use Robo\Result;

/**
 * A Robo "after" task is a task that may be added
 * to a collection via Collection::after().  An
 * after task is given a copy of the incremental
 * result from the tasks in the collection that have
 * been run so far, which it may reference or modify
 * before returning.
 *
 * Interface AfterTaskInterface
 * @package Robo\Contract
 */
interface AfterTaskInterface
{
    /**
     * @return \Robo\Result
     */
    public function run(Result $incrementalResult);
}
