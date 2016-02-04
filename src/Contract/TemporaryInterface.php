<?php
namespace Robo\Contract;

/**
 * Any Robo tasks that creates temporary objects should
 * implement TemporaryInterface, and use Temporary.
 * It should utilize TemporaryManager::temporaryTask() in
 * its loadTasks and loadShortcuts methods.
 * @see Robo\Task\FileSystem\loadTasks::taskTmpDir
 *
 * The effect of doing this is that the cleanupTemporaries()
 * method will be called on rollback and completion. Clients
 * will also have access to a setTemporary() method; if set
 * to false, then cleanupTemporaries() will be called on
 * rollback, but not on completion.  Call this function in
 * the task constructor to make a task whose temporaries are
 * persistent by default, but that can be made temporary
 * via setTemporary(true).
 *
 * Interface CompletionInterface
 * @package Robo\Contract
 */
interface TemporaryInterface extends RollbackInterface, CompletionInterface
{
    /**
     * Revert an operation that can be rolled back
     */
    public function cleanupTemporaries();
}
