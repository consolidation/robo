<?php
namespace Robo\Collection;

use Consolidation\AnnotationCommand\ProcessResultInterface;
use Robo\Contract\TaskInterface;

/**
 * The collection process hook is added to the annotation command
 * hook manager in Runner::configureContainer(). This hook will be
 * called every time a command runs.  If the command result is a
 * \Robo\Contract\TaskInterface (in particular, \Robo\Collection\Collection),
 * then we run the collection, and return the result.  We ignore results
 * of any other type.
 */
class CollectionProcessHook implements ProcessResultInterface
{
    public function process($result, array $args)
    {
        if ($result instanceof TaskInterface) {
            return $result->run();
        }
    }
}
