<?php
namespace Robo\Container;

use League\Container\Container;

use Robo\Contract\CompletionInterface;
use Robo\Collection\Temporary;
use Robo\Contract\TaskInterface;
use Robo\Collection\Collection;
use Robo\Collection\CompletionWrapper;
use Robo\Collection\NestedCollectionInterface;
use Robo\Task\Simulator;

class RoboContainer extends Container
{
    /**
     * @var boolean
     */
    protected $simulated;

    public function __construct(
        ServiceProviderAggregateInterface $providers = null,
        InflectorAggregateInterface $inflectors = null,
        DefinitionFactoryInterface $definitionFactory = null
    ) {
        parent::__construct($providers, $inflectors, $definitionFactory);
        $this->simulated = false;
    }

    public function setSimulated($simulated)
    {
        $this->simulated = $simulated;
    }

    public function isSimulated()
    {
        return $this->simulated;
    }

    /**
     * Get a serivice. Apply any service decorators.
     */
    public function get($alias, array $args = [])
    {
        $service = parent::get($alias, $args);

        // Do not wrap our wrappers.
        if ($service instanceof CompletionWrapper || $service instanceof Simulator) {
            return $service;
        }

        // Remember whether or not this is a task before
        // it gets wrapped in any service decorator.
        $isTask = $service instanceof TaskInterface;
        $isCollection = $service instanceof NestedCollectionInterface;

        // If the task implements CompletionInterface, ensure
        // that its 'complete' method is called when the application
        // terminates -- but only if its 'run' method is called
        // first.  If the task is added to a collection, then the
        // task will be unwrapped via its `original` method, and
        // it will be re-wrapped with a new completion wrapper for
        // its new collection.
        if ($service instanceof CompletionInterface) {
            $service = parent::get('completionWrapper', [Temporary::getCollection(), $service]);
        }

        // If we are in simulated mode, then wrap any task in
        // a TaskSimulator.
        if ($isTask && !$isCollection && ($this->isSimulated())) {
            $service = parent::get('simulator', [$service, $args]);
        }

        return $service;
    }
}
