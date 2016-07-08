<?php
namespace Robo\Collection;

use Psr\Log\LogLevel;
use Robo\Contract\TaskInterface;

interface NestedCollectionInterface
{
    public function setParentCollection(NestedCollectionInterface $parentCollection);
    public function getParentCollection();
    public function registerRollback(TaskInterface $rollbackTask);
    public function registerCompletion(TaskInterface $completionTask);
}
