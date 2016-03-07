<?php
namespace Robo;

use Psr\Log\LoggerInterface;
use Robo\Collection\Temporary;
use Robo\Task\Simulator;
use Robo\Config;

class TaskAssembler
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var boolean
     */
    protected $simulated;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
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
        if ($this->isSimulated() || Config::isSimulated()) {
            $task = new Simulator($task, $taskConstructorParameters);
        }

        return $task;
    }
}
