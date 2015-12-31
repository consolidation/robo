<?php
namespace Robo\Contract;

/**
 * Any Robo tasks that creates transient objects should
 * implement TransientInterface, and use Transient.
 * It should utilize TransientManager::transientTask() in
 * its loadTasks and loadShortcuts methods.
 * @see Robo\Task\FileSystem\loadTasks::taskTmpDir
 *
 * The effect of doing this is that the cleanupTransients()
 * method will be called on rollback and completion. Clients
 * will also have access to a setTransient() method; if set
 * to false, then cleanupTransients() will be called on
 * rollback, but not on completion.  Call this function in
 * the task constructor to make a task whose transients are
 * persistent by default, but that can be made transient
 * via setTransient(true).
 *
 * Interface CompletionInterface
 * @package Robo\Contract
 */
interface TransientInterface extends RollbackInterface, CompletionInterface
{
    /**
     * Revert an operation that can be rolled back
     */
    function cleanupTransients();
}
