<?php
namespace Robo;

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\ConsoleOutput;

trait Output {
    private static $output;
    private static $dialog;

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
        return $this->getDialogHelper()->ask($this->getOutput(), "<question>?  $question</question> ");
    }

    /**
     * @return ConsoleOutput
     */
    protected function getOutput()
    {
        if (!static::$output) {
            static::$output = new ConsoleOutput();
        }
        return static::$output;
    }

    public static function setOutput(ConsoleOutput $output)
    {
        static::$output = $output;
    }

    protected function getDialogHelper()
    {
        if (!static::$dialog) {
            static::$dialog = new DialogHelper();
        }
        return static::$dialog;
    }

    public static function setDialogHelper(DialogHelper $helper)
    {
        static::$dialog = $helper;
    }

    private function writeln($text)
    {
        $this->getOutput()->writeln($text);
    }

}
 
