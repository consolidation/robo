<?php
namespace Robo\Contract;

use Robo\Result;

/**
 * A Robo filter task is a task that may be added
 * to a collection via Collection::before() or
 * Collection::after().  A filter task is given a
 * reference to the incremental result, which it may
 * modify and return.
 *
 * Interface FilterTaskInterface
 * @package Robo\Contract
 */
interface FilterTaskInterface
{
    /**
     * @return \Robo\Result
     */
    public function run(Result $incrementalResult);
}
