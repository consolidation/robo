<?php
namespace Robo\Collection;

use Consolidation\AnnotatedCommand\Hooks\ProcessResultInterface;
use Consolidation\AnnotatedCommand\AnnotationData;
use Robo\Contract\TaskInterface;
use Robo\Result;

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
    public function process($result, array $args, AnnotationData $annotations)
    {
        if ($result instanceof TaskInterface) {
            try {
                return $result->run();
            } catch (\Exception $e) {
                return Result::fromException($result, $e);
            }
        }
    }
}
