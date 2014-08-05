<?php
namespace Robo;

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\ConsoleOutput;

trait Output {


    protected function say($text)
    {
        $char = strncasecmp(PHP_OS, 'WIN', 3) == 0 ? '>' : '➜';
        $this->writeln("$char  $text");
    }

    protected function yell($text, $length = 40)
    {
        $char = strncasecmp(PHP_OS, 'WIN', 3) == 0 ? ' ' : '➜';
        $format = "$char  <fg=white;bg=green;options=bold>%s</fg=white;bg=green;options=bold>";
        $text = str_pad($text, $length, ' ', STR_PAD_BOTH);
        $len = strlen($text) + 4;
        $space = str_repeat(' ', $len);
        $this->writeln(sprintf($format, $space));
        $this->writeln(sprintf($format, " $text "));
        $this->writeln(sprintf($format, $space));
    }

    protected function printTaskInfo($text, $task = null)
    {
        if (!$task) $task = $this;
        $this->writeln(" <fg=white;bg=cyan;options=bold>[".get_class($task)."]</fg=white;bg=cyan;options=bold> $text");
    }

    protected function ask($question, $hideAnswer = false)
    {
        $dialog = $this->getDialog();
        if ($hideAnswer) {
            return $dialog->askHiddenResponse($this->getOutput(), "<question>?  $question</question> ");
        } else {
            return $dialog->ask($this->getOutput(), "<question>?  $question</question> ");
        }
    }

    /**
     * @return ConsoleOutput
     */
    private function getOutput()
    {
        return Runner::getPrinter();
    }

    private function getDialog()
    {
        return new DialogHelper();
    }

    private function writeln($text)
    {
        $this->getOutput()->writeln($text);
    }

}
 
