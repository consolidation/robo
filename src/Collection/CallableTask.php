<?php
namespace Robo\Collection;

use Robo\Result;
use Robo\Contract\TaskInterface;
use Robo\Collection\Collection;

/**
 * Creates a task wrapper that converts any Callable into an
 * object that can be used directly with a task collection.
 *
 * It is not necessary to use this class directly; Collection will
 * automatically wrap Callables when they are added.
 */
class CallableTask implements TaskInterface
{
    private $fn;

    public function __construct($fn)
    {
        $this->fn = $fn;
    }

    public function run()
    {
        $result = call_user_func($this->fn);
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
