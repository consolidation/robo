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
        // TODO: If we ever want to convert the simulated task back into
        // an executable task, then we should save the wrapped task.
        $this->task = ($task instanceof WrappedTaskInterface) ? $task->original() : $task;
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
            $callchain .= "\n    ->$command(<fg=green>$parameters</>)";
        }
        // RoboLogLevel::SIMULATED_ACTION
        $this->logger()->log(
            RoboLogLevel::SIMULATED_ACTION,
            "Simulating {simulated}({parameters})$callchain",
            $this->getTaskContext(
                [
                    'simulated' => TaskInfo::formatTaskName($this->task),
                    'parameters' => $this->formatParameters($this->constructorParameters),
                    '_style' => ['simulated' => 'fg=blue;options=bold'],
                ]
            )
        );
        return Result::success($this);
    }

    protected function formatParameters($action)
    {
        $parameterList = array_map(
            function ($item) {
                if (is_callable($item)) {
                    return 'inline_function(...)';
                }
                if (is_array($item)) {
                    return var_export($item, true);
                }
                if (is_object($item)) {
                    return '[object]';
            //                    return var_export($item, true);
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
