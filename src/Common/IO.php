<?php
namespace Robo\Common;

use Robo\Robo;
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
        if (!Robo::hasService('output')) {
            return new NullOutput();
        }
        return Robo::output();
    }

    /**
     * @return InputInterface
     */
    protected function getInput()
    {
        if (!Robo::hasService('input')) {
            return new ArgvInput();
        }
        return Robo::input();
    }

    protected function decorationCharacter($nonDecorated, $decorated)
    {
        if (!$this->getOutput()->isDecorated() || (strncasecmp(PHP_OS, 'WIN', 3) == 0)) {
            return $nonDecorated;
        }
        return $decorated;
    }

    protected function say($text)
    {
        $char = $this->decorationCharacter('>', '➜');
        $this->writeln("$char  $text");
    }

    protected function yell($text, $length = 40, $color = 'green')
    {
        $char = $this->decorationCharacter(' ', '➜');
        $format = "$char  <fg=white;bg=$color;options=bold>%s</fg=white;bg=$color;options=bold>";
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
