<?php
namespace Robo;

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\ConsoleOutput;

trait Output {
    protected function say($text)
    {
        $this->writeln("➜  $text");
    }

    protected function printTaskInfo($text, $task = null)
    {
        if (!$task) $task = $this;
        $this->writeln(" <fg=white;bg=cyan;options=bold>[".get_class($task)."]</fg=white;bg=cyan;options=bold> $text");
    }

    protected function ask($question)
    {
        return (new DialogHelper())->ask($this->getOutput(), "<question>?  $question</question>");
    }

    /**
     * @return ConsoleOutput
     */
    protected function getOutput()
    {
        static $output;
        if (!$output) {
            $output = new ConsoleOutput();
        }
        return $output;
    }

    private function writeln($text)
    {
        $this->getOutput()->writeln($text);
    }

}
 