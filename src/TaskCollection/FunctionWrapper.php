<?php
namespace Robo\TaskCollection;

use Robo\Result;
use Robo\Contract\TaskInterface;
use Robo\Contract\CollectedInterface;
use Robo\TaskCollection\Collection;

/**
 * Creates a task wrapper that converts a function pointer into an
 * interface that can be used directly with a task collection.
 *
 * Clients usually do not need to use this class directly; when an
 * anonymous function added to a task collection via the add() method, it
 * will automatically be wrapped in a function wrapper so that it presents
 * a unified interface for the collection.
 */
class FunctionWrapper implements TaskInterface, CollectedInterface
{
    private $fn;
    private $collection = null;

    public function __construct(\Closure $fn, $collection = null)
    {
        $this->fn = $fn;
        $this->collection = $collection;
    }

    public function collected(Collection $collection)
    {
        // If this function wrapper already has a reference to
        // the correct collection, then we can use this instance.
        if ($collection == $this->collection) {
            return $this;
        }
        // Otherwise, make a new one.
        return new self($this->fn, $collection);
    }

    public function run()
    {
        $resultsFromPreviousTasks = null;
        if ($this->collection) {
            $resultsFromPreviousTasks = $this->collection->getIncrementalResults();
        }
        $fn = $this->fn;
        $result = $fn($resultsFromPreviousTasks);
        // If the function returns no result, then count it
        // as a success.
        if (!isset($result)) {
            $result = Result::success($this);
        }
        // If the function returns a result, it must either return
        // a \Robo\Result or an exit code.  In the later case, we
        // convert it to a \Robo\Result.
        if (!$result instanceof Result) {
            $result = new Result($this, $result);
        }

        return $result;
    }
}
