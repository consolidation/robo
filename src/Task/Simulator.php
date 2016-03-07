<?php
namespace Robo\Task;

use Robo\TaskInfo;
use Robo\Result;
use Robo\Contract\TaskInterface;
use Robo\Log\RoboLogLevel;

class Simulator extends BaseTask
{
    protected $task;
    protected $constructorParameters;
    protected $stack = [];

    public function __construct(TaskInterface $task, $constructorParameters)
    {
        $this->task = $task;
        $this->constructorParameters = $constructorParameters;
    }

    public function __call($function, $args)
    {
        $this->stack[] = array_merge([$function], $args);
        return $this;
    }

    public function run()
    {
        $callchain = '';
        foreach ($this->stack as $action) {
            $command = array_shift($action);
            $parameters = $this->formatParameters($action);
            $callchain .= "\n    ->$command($parameters)";
        }
        // RoboLogLevel::SIMULATED_ACTION
        $this->logger()->log(RoboLogLevel::SIMULATED_ACTION, 'Simulating {simulated}({parameters}){callchain}',
            $this->getTaskContext(
                [
                    'simulated' => TaskInfo::formatTaskName($this->task),
                    'parameters' => $this->formatParameters($this->constructorParameters),
                    'callchain' => $callchain,
                ]
            )
        );
        return Result::success($this);
    }

    protected function formatParameters($action) {
        $parameterList = array_map(
            function($item) {
                if (is_array($item) || is_object($item)) {
                    return var_export($item, true);
                }
                if (is_string($item)) {
                    return "'$item'";
                }
                return $item;
            },
            $action
        );
        return implode(', ', $parameterList);
    }

}
