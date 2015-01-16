<?php
namespace Robo\Common;

use Robo\Config;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

trait IO
{
    /**
     * @return OutputInterface
     */
    protected function getOutput()
    {
        return Config::get('output', new NullOutput());
    }

    /**
     * @return InputInterface
     */
    protected function getInput()
    {
        return new ArgvInput();
    }

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
        $len = strlen($text) + 2;
        $space = str_repeat(' ', $len);
        $this->writeln(sprintf($format, $space));
        $this->writeln(sprintf($format, " $text "));
        $this->writeln(sprintf($format, $space));
    }

    protected function ask($question, $hideAnswer = false)
    {
        if ($hideAnswer) {
            return $this->askHidden($question);
        }
        return $this->doAsk(new Question($this->formatQuestion($question)));
    }
    
    protected function askHidden($question)
    {
        $question = new Question($this->formatQuestion($question));
        $question->setHidden(true);
        return $this->doAsk($question);
    }

    protected function askDefault($question, $default)
    {
        return $this->doAsk(new Question($this->formatQuestion("$question [$default]"), $default));
    }

    protected function confirm($question)
    {
        return $this->doAsk(new ConfirmationQuestion($this->formatQuestion($question . ' (y/n)'), false));
    }

    private function doAsk(Question $question)
    {
        return $this->getDialog()->ask($this->getInput(), $this->getOutput(), $question);
    }

    private function formatQuestion($message)
    {
        return  "<question>?  $message</question> ";
    }

    protected function getDialog()
    {
        return new QuestionHelper();
    }

    private function writeln($text)
    {
        $this->getOutput()->writeln($text);
    }

}
