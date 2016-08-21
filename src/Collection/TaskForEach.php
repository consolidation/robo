<?php
namespace Robo\Collection;

use Robo\Result;
use Robo\TaskInfo;
use Robo\Task\BaseTask;
use Robo\Contract\TaskInterface;
use Robo\Collection\NestedCollectionInterface;
use Robo\Contract\BuilderAwareInterface;
use Robo\Common\BuilderAwareTrait;

/**
 * Creates a task wrapper that converts any Callable into an
 * object that will execute the callback once for each item in the
 * provided collection.
 *
 * It is not necessary to use this class directly; Collection::addIterable
 * will automatically create one when it is called.
 */
class TaskForEach extends BaseTask implements NestedCollectionInterface, BuilderAwareInterface
{
    use BuilderAwareTrait;

    protected $functionStack = [];
    protected $countingStack = [];
    protected $message;
    protected $context = [];
    protected $iterable;
    protected $parentCollection;

    public function __construct($iterable)
    {
        $this->iterable = $iterable;
    }

    public function iterationMessage($message, $context = [])
    {
        $this->message = $message;
        $this->context = $context + ['name' => 'Progress'];
        return $this;
    }

    protected function showIterationMessage($key, $value)
    {
        if ($this->message) {
            $context = ['key' => $key, 'value' => $value];
            $context += $this->context;
            $context += TaskInfo::getTaskContext($this);
            $this->printTaskInfo($this->message, $context);
        }
    }

    public function withEachKeyValueCall(callable $fn)
    {
        $this->functionStack[] = $fn;
        return $this;
    }

    public function call(callable $fn)
    {
        return $this->withEachKeyValueCall(
            function ($key, $value) use ($fn) {
                return call_user_func($fn, $value);
            }
        );
    }

    public function withBuilder(callable $fn)
    {
        $this->countingStack[] =
            function ($key, $value) use ($fn) {
                // Create a new builder for every iteration
                $builder = $this->collectionBuilder();
                // The user function should build task operations using
                // the $key / $value parameters; we will call run() on
                // the builder thus constructed.
                call_user_func($fn, $builder, $key, $value);
                return $builder->getCollection()->progressIndicatorSteps();
            };
        return $this->withEachKeyValueCall(
            function ($key, $value) use ($fn) {
                // Create a new builder for every iteration
                $builder = $this->collectionBuilder()
                    ->setParentCollection($this->parentCollection);
                // The user function should build task operations using
                // the $key / $value parameters; we will call run() on
                // the builder thus constructed.
                call_user_func($fn, $builder, $key, $value);
                return $builder->run();
            }
        );
    }

    public function setParentCollection(NestedCollectionInterface $parentCollection)
    {
        $this->parentCollection = $parentCollection;
        return $this;
    }

    public function progressIndicatorSteps()
    {
        $multiplier = count($this->functionStack);
        if (!empty($this->countingStack)) {
            $value = reset($this->iterable);
            $key = key($this->iterable);
            foreach ($this->countingStack as $fn) {
                $multiplier += call_user_func($fn, $key, $value);
            }
        }
        return count($this->iterable) * $multiplier;
    }

    public function run()
    {
        $finalResult = Result::success($this);
        $this->startProgressIndicator();
        foreach ($this->iterable as $key => $value) {
            $this->showIterationMessage($key, $value);
            try {
                foreach ($this->functionStack as $fn) {
                    $result = call_user_func($fn, $key, $value);
                    $this->advanceProgressIndicator();
                    if (!isset($result)) {
                        $result = Result::success($this);
                    }
                    // If the function returns a result, it must either return
                    // a \Robo\Result or an exit code.  In the later case, we
                    // convert it to a \Robo\Result.
                    if (!$result instanceof Result) {
                        $result = new Result($this, $result);
                    }
                    if (!$result->wasSuccessful()) {
                        return $result;
                    }
                    $finalResult = $result->merge($finalResult);
                }
            } catch (\Exception $e) {
                return Result::fromException($result, $e);
            }
        }
        $this->stopProgressIndicator();
        return $finalResult;
    }
}
