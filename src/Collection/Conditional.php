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
 * Conditional execution of collections
 *
 * Usually, it is best if a Robo command does all of its conditional
 * testing using procedural code, before creating any tasks.  However,
 * sometimes a particular test cannot run until some prior tasks
 * finish running.  In those instances, there are two options:
 *
 * (1) Run one collection to set up, and then run any needed tests,
 *     and build a second collection of tasks based on the result.
 *
 * (2) Use a Conditional task.
 *
 * The conditional task creates a task wrapper that holds three collections:
 *
 * check - runs unconditionally and records its result
 * onTrue - runs if the check collection returns successfully
 * onFalse - runs if the check collection fails
 *
 * Optionally, a 'test' function may be provided.  If it is,
 * then it will be passed the result object from the check
 * collection.  The result of the test function will be used
 * to determine whether the 'onTrue' or 'onFalse' collection
 * is called.
 */
class Conditional extends BaseTask implements NestedCollectionInterface
{
    protected $checkCollection;
    protected $onTrueCollection;
    protected $onFalseCollection;

    protected $testFunction = false;

    protected $stepsInOnTrueCollection = 0;
    protected $stepsInOnFalseCollection = 0;

    protected $parentCollection;

    public function __construct($checkCollection)
    {
        $this->checkCollection = $checkCollection;
    }

    public function setParentCollection(NestedCollectionInterface $parentCollection)
    {
        $this->parentCollection = $parentCollection;
        return $this;
    }

    public function test($callable)
    {
        $this->testFunction = $callable;
        return $this;
    }

    public function onTrue($onTrueCollection)
    {
        $this->onTrueCollection = $onTrueCollection;
    }

    public function onFalse($onFalseCollection)
    {
        $this->onFalseCollection = $onFalseCollection;
    }

    public function progressIndicatorSteps()
    {
        $this->checkCollection->setParentCollection($this->parentCollection);
        $this->onTrueCollection->setParentCollection($this->parentCollection);
        $this->onFalseCollection->setParentCollection($this->parentCollection);

        $stepsInCheckCollection = $this->checkCollection->progressIndicatorSteps();
        $this->stepsInOnTrueCollection = $this->onTrueCollection->progressIndicatorSteps();
        $this->stepsInOnFalseCollection = $this->onFalseCollection->progressIndicatorSteps();

        return $stepsInCheckCollection + max($this->stepsInOnTrueCollection, $this->stepsInOnFalseCollection);
    }

    protected function determineResult($result)
    {
        if ($this->testFunction) {
            return call_user_func($this->testFunction, $result);
        }
        return $result->wasSuccessful();
    }

    public function run()
    {
        $result = $this->checkCollection->run();

        $checkResult = $this->determineResult($result);

        $collectionToRun = $this->onTrueCollection;
        $missedSteps = $this->stepsInOnFalseCollection - $this->stepsInOnTrueCollection;
        if (!$checkResult) {
            $collectionToRun = $this->onFalseCollection;
            $missedSteps = $this->stepsInOnTrueCollection - $this->stepsInOnFalseCollection;
        }

        $result = $collectionToRun->run();
        // When we count how many progress indicator steps there are for this task,
        // we consider the MAXIMUM value of the 'onTrue' and 'onFalse' steps, and
        // go with that.  If we run the collection with fewer steps, then we just
        // "make up the difference" at the end by quickly advancing the progress bar.
        if ($missedSteps > 0) {
            $this->advanceProgressIndicator($missedSteps);
        }
        return $result;
    }
}
