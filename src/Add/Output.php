<?php
namespace Robo\Add;

use Symfony\Component\Console\Output\ConsoleOutput;

trait Output {

    /**
     * @var \Symfony\Component\Console\Output\ConsoleOutput
     */
    private $output;

    protected function say($text)
    {
        $this->writeln("➜  <comment>$text</comment>");
    }

    protected function taskInfo($text)
    {
        $this->writeln("<info>[".__CLASS__."]</info> $text");
    }

    private function writeln($text)
    {
        static $output;
        if (!$output) {
            $output = new ConsoleOutput();
        }
        $output->writeln($text);
    }

}
 