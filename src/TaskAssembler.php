<?php
namespace Robo;

use Robo\Collection\Temporary;
use Robo\Task\Simulator;
use Robo\Config;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Put together tasks
 */
class TaskAssembler implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var boolean
     */
    protected $simulated;

    public function __construct()
    {
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

    public function assemble($taskClass, $taskConstructorParameters = [])
    {
        $class = new \ReflectionClass($taskClass);
        $task = $class->newInstanceArgs($taskConstructorParameters);

        // TODO: Would be more rigorous to have an interface for this.
        if (method_exists($task, 'setLogger')) {
            $task->setLogger($this->logger);
        }

        // If the task implements CompletionInterface, ensure
        // that its 'complete' method is called when the application
        // terminates -- but only if its 'run' method is called
        // first.  If the task is added to a collection, then its
        // complete method will be called after the collection completes.
        if ($task instanceof CompletionInterface) {
            $task = Temporary::wrap($task);
        }

        // If we are in simulated mode, then wrap the task in
        // a TaskSimulator.
        if ($this->isSimulated()) {
            $task = new Simulator($task, $taskConstructorParameters);
        }

        return $task;
    }
}
