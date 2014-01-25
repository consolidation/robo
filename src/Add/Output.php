<?php
namespace Robo\Add;

use Symfony\Component\Console\Output\ConsoleOutput;

trait Output {
    protected function say($text)
    {
        $this->writeln("➜  $text");
    }

    protected function printTaskInfo($text)
    {
        $this->writeln(" <fg=white;bg=cyan;options=bold>[".get_class($this)."]</fg=white;bg=cyan;options=bold> $text");
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
 