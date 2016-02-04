<?php
namespace Robo\Collection;

use Robo\Result;
use Robo\Contract\TaskInterface;
use Robo\Contract\CollectedInterface;
use Robo\Collection\Collection;

/**
 * Creates a task wrapper that converts any Callable into an
 * object that can be used directly with a task collection.
 *
 * It is not necessary to use this class directly; Collection will
 * automatically wrap Callables when they are added.
 */
class CallableTask implements TaskInterface, CollectedInterface
{
    private $fn;
    private $collection = null;

    public function __construct($fn, $collection = null)
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
        $result = call_user_func($this->fn, $resultsFromPreviousTasks);
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
