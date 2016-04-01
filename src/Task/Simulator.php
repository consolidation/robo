<?php
namespace Robo\Task;

use Robo\TaskInfo;
use Robo\Result;
use Robo\Contract\TaskInterface;
use Robo\Log\RoboLogLevel;
use Psr\Log\LogLevel;

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
        $result = call_user_func_array([$this->task, $function], $args);
        return $result == $this->task ? $this : $result;
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
            LogLevel::NOTICE,
            "Simulating {simulated}({parameters})$callchain",
            $this->getTaskContext(
                [
                    '_level' => RoboLogLevel::SIMULATED_ACTION,
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
