<?php
namespace Robo\Task;

use Robo\TaskInfo;
use Robo\Result;
use Robo\Contract\TaskInterface;
use Robo\Contract\SimulatedInterface;
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
        $context = $this->getTaskContext(
            [
                '_level' => RoboLogLevel::SIMULATED_ACTION,
                'simulated' => TaskInfo::formatTaskName($this->task),
                'parameters' => $this->formatParameters($this->constructorParameters),
                '_style' => ['simulated' => 'fg=blue;options=bold'],
            ]
        );

        // RoboLogLevel::SIMULATED_ACTION
        $this->printTaskInfo(
            "Simulating {simulated}({parameters})$callchain",
            $context
        );

        $result = null;
        if ($this->task instanceof SimulatedInterface) {
            $result = $this->task->simulate($context);
        }
        if (!isset($result)) {
            $result = Result::success($this);
        }

        return $result;
    }

    protected function formatParameters($action)
    {
        $parameterList = array_map([$this, 'convertParameter'], $action);
        return implode(', ', $parameterList);
    }

    protected function convertParameter($item)
    {
        if (is_callable($item)) {
            return 'inline_function(...)';
        }
        if (is_array($item)) {
            return $this->shortenParameter(var_export($item, true));
        }
        if (is_object($item)) {
            return $this->shortenParameter(var_export($item, true), '[' . get_class($item). 'object]');
        }
        if (is_string($item)) {
            return $this->shortenParameter("'$item'");
        }
        return $item;
    }

    protected function shortenParameter($item, $shortForm = '')
    {
        $maxLength = 80;
        $tailLength = 20;
        if (strlen($item) < $maxLength) {
            return $item;
        }
        if (!empty($shortForm)) {
            return $shortForm;
        }
        $item = trim($item);
        $tail = preg_replace("#.*\n#ms", '', substr($item, -$tailLength));
        $head = preg_replace("#\n.*#ms", '', substr($item, 0, $maxLength - (strlen($tail) + 5)));
        return "$head ... $tail";
    }
}
