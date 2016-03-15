<?php
namespace Robo\Container;

use League\Container\Container;

use Robo\Contract\CompletionInterface;
use Robo\Collection\Temporary;
use Robo\Task\Simulator;
use Robo\Contract\TaskInterface;
use Robo\Collection\Collection;

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

        // Remember whether or not this is a task before
        // it gets wrapped in any service decorator.
        $isTask = $service instanceof TaskInterface;
        $isCollection = $service instanceof Collection;

        // If the task implements CompletionInterface, ensure
        // that its 'complete' method is called when the application
        // terminates -- but only if its 'run' method is called
        // first.  If the task is added to a collection, then its
        // complete method will be called after the collection completes.
        if ($service instanceof CompletionInterface) {
            $service = Temporary::wrap($service);
        }

        // If we are in simulated mode, then wrap any task in
        // a TaskSimulator.
        if ($isTask && !$isCollection && ($this->isSimulated())) {
            $service = new Simulator($service, $args);
        }

        return $service;
    }
}
