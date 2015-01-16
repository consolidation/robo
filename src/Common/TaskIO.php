<?php
namespace Robo\Common;

trait TaskIO 
{
    use IO;

    protected function printTaskInfo($text, $task = null)
    {
        if (!$task) {
            $task = $this;
        }

        $name = get_class($task);
        $parts = explode('\\', $name);
        $name = array_pop($parts);

        $this->writeln(" <fg=white;bg=cyan;options=bold>[$name]</fg=white;bg=cyan;options=bold> $text");
    }
}