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
use Symfony\Component\Console\Style\SymfonyStyle;

trait IO
{
    use InputAwareTrait;
    use OutputAwareTrait;

    /** var: SymfonyStyle */
    protected $io;

    /**
     * Provide access to SymfonyStyle object.
     * See: http://symfony.com/blog/new-in-symfony-2-8-console-style-guide
     * @return SymfonyStyle
     */
    protected function io()
    {
        if (!$this->io) {
            $this->io = new SymfonyStyle($this->input(), $this->output());
        }
        return $this->io;
    }

    protected function decorationCharacter($nonDecorated, $decorated)
    {
        if (!$this->output()->isDecorated() || (strncasecmp(PHP_OS, 'WIN', 3) == 0)) {
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
        $this->formattedOutput($text, $length, $format);
    }

    private function formattedOutput($text, $length, $format)
    {
        $lines = explode("\n", trim($text, "\n"));
        $maxLineLength = array_reduce(array_map('strlen', $lines), 'max');
        $length = max($length, $maxLineLength);
        $len = $length + 2;
        $space = str_repeat(' ', $len);
        $this->writeln(sprintf($format, $space));
        foreach ($lines as $line) {
            $line = str_pad($line, $length, ' ', STR_PAD_BOTH);
            $this->writeln(sprintf($format, " $line "));
        }
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
        return $this->getDialog()->ask($this->input(), $this->output(), $question);
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
        $this->output()->writeln($text);
    }
}
