<?php
namespace Robo\Collection;

use Psr\Log\LogLevel;
use Robo\Contract\TaskInterface;

interface CollectionInterface
{
    // Unnamed tasks are assigned an arbitrary numeric index
    // in the task list. Any numeric value may be used, but the
    // UNNAMEDTASK constant is recommended for clarity.
    const UNNAMEDTASK = 0;

    // Public API
    public function add(TaskInterface $task, $name = self::UNNAMEDTASK);
    public function addCode(callable $task, $name = self::UNNAMEDTASK);
    public function rollback(TaskInterface $rollbackTask);
    public function rollbackCode(callable $rollbackTask);
    public function completion(TaskInterface $completionTask);
    public function completionCode(callable $completionTask);
    public function before($name, $task, $nameOfTaskToAdd = self::UNNAMEDTASK);
    public function after($name, $task, $nameOfTaskToAdd = self::UNNAMEDTASK);
    public function progressMessage($text, $context = [], $level = LogLevel::NOTICE);

    // Internal methods
    public function setParentCollection(CollectionInterface $parentCollection);
    public function getParentCollection();
    public function registerRollback(TaskInterface $rollbackTask);
    public function registerCompletion(TaskInterface $completionTask);
}
