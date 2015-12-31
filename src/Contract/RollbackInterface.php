<?php
namespace Robo\Contract;

/**
 * All Robo tasks should implement this interface.
 * Task should be configured by chained methods.
 *
 * Interface TaskInterface
 * @package Robo\Contract
 */
interface RollbackInterface
{
    /**
     * Revert an operation that can be rolled back
     */
    function rollback();
}
