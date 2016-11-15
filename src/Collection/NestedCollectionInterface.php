<?php
namespace Robo\Collection;

use Psr\Log\LogLevel;
use Robo\Contract\TaskInterface;

interface NestedCollectionInterface
{
    /**
     * @param \Robo\Collection\NestedCollectionInterface $parentCollection
     *
     * @return $this
     */
    public function setParentCollection(NestedCollectionInterface $parentCollection);
}
