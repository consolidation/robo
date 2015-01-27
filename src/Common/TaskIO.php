<?php
namespace Robo\Common;

trait TaskIO 
{
    use IO;

    protected function printTaskInfo($text, $task = null)
    {
        $name = $this->getPrintedTaskName($task);
        $this->writeln(" <fg=white;bg=cyan;options=bold>[$name]</fg=white;bg=cyan;options=bold> $text");
    }

    protected function printTaskSuccess($text, $task = null)
    {
        $name = $this->getPrintedTaskName($task);
        $this->writeln(" <fg=white;bg=green;options=bold>[$name]</fg=white;bg=green;options=bold> $text");
    }

    protected function printTaskError($text, $task = null)
    {
        $name = $this->getPrintedTaskName($task);
        $this->writeln(" <fg=white;bg=red;options=bold>[$name]</fg=white;bg=red;options=bold> $text");
    }

    protected function getPrintedTaskName($task = null)
    {
        if (!$task) {
            $task = $this;
        }
        $name = get_class($task);
        $name = preg_replace('~Stack^~', '' , $name);
        $name = str_replace('Robo\Task\Base\\', '' , $name);
        $name = str_replace('Robo\Task\\', '' , $name);
        return $name;
    }
}