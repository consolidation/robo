<?php
namespace Robo\Contract;

/**
 * Any Robo tasks that implements this interface will
 * be called when the task collection it is added to
 * fails, and runs its rollback operation.
 *
 * Rollback operations can also be managed as temporary
 * tasks, just as in the CompletionInterface. However,
 * in this instance, the rollback operation will only
 * be done if TemporaryManager::fail() is explicitly called.
 *
 * Tasks that implement RollbackInterface should consider
 * implementing TemporaryInterface instead.
 *
 * Interface RollbackInterface
 * @package Robo\Contract
 */
interface RollbackInterface
{
    /**
     * Revert an operation that can be rolled back
     */
    public function rollback();
}
