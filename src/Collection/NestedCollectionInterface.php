<?php
namespace Robo\Collection;

use Psr\Log\LogLevel;
use Robo\Contract\TaskInterface;

interface NestedCollectionInterface
{
    public function setParentCollection(NestedCollectionInterface $parentCollection);
}
