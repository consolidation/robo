<?php
namespace Robo\Contract;

/**
 * All Robo tasks should implement this interface.
 * Task should be configured by chained methods.
 *
 * Interface CompletionInterface
 * @package Robo\Contract
 */
interface CompletionInterface
{
    /**
     * Revert an operation that can be rolled back
     */
    function complete();
}
