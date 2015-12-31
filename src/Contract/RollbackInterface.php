<?php
namespace Robo\Contract;

/**
 * Any Robo tasks that implements this interface will
 * be called when the task collection it is added to
 * fails, and runs its rollback operation.
 *
 * Rollback operations can also be managed as transient
 * tasks, just as in the CompletionInterface. However,
 * in this instance, the rollback operation will only
 * be done if TransientManager::fail() is explicitly called.
 *
 * Tasks that implement RollbackInterface should consider
 * implementing TransientInterface instead.
 *
 * Interface RollbackInterface
 * @package Robo\Contract
 */
interface RollbackInterface
{
    /**
     * Revert an operation that can be rolled back
     */
    function rollback();
}
